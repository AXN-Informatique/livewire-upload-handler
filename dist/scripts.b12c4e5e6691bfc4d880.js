/******/ (() => { // webpackBootstrap
/*!*********************************!*\
  !*** ./resources/js/scripts.js ***!
  \*********************************/

document.addEventListener('alpine:init', () => {
    let _nextUploadTime = 0
    let _dropzoneDisabled = false

    function _handleDropzone(container, dropCallback) {
        container.addEventListener('dragover', (e) => {
            e.preventDefault()

            if (! _dropzoneDisabled) {
                container.classList.add('luh__dropzone-dragging')
            }
        })

        container.addEventListener('dragleave', (e) => {
            container.classList.remove('luh__dropzone-dragging')
        })

        container.addEventListener('drop', (e) => {
            e.preventDefault()
            container.classList.remove('luh__dropzone-dragging')

            if (! _dropzoneDisabled) {
                dropCallback(e)
            }
        })
    }

    function _validateFile(file, errors, $wire) {
        if ($wire.acceptsMimeTypes.length > 0 && ! $wire.acceptsMimeTypes.includes(file.type)) {
            errors[file.name] = window.livewireUploadHandlerParams.invalidFileTypeErrorMessage
            return false
        }

        if ($wire.maxFileSize !== null && file.size > $wire.maxFileSize) {
            errors[file.name] = window.livewireUploadHandlerParams.fileTooLoudErrorMessage
            return false
        }

        return true
    }

    function _waitBeforeNextUpload() {
        _nextUploadTime = Math.max(_nextUploadTime, Date.now())
        const timeToWait = _nextUploadTime - Date.now()
        _nextUploadTime += 1050

        return new Promise(r => setTimeout(r, timeToWait))
    }

    function _compressImage(file, settings) {
        const compressorjs = eval(window.livewireUploadHandlerParams.compressorjsVar);

        if (! compressorjs || ! file.type.startsWith('image/')) {
            return file
        }

        return new Promise((resolve, reject) => {
            settings.success = resolve
            settings.error = reject

            new compressorjs(file, settings)
        })
    }

    // ALPINE COMPONENT FOR GROUP
    // =========================================================================

    Alpine.data('LivewireUploadHandlerGroup', ($wire) => ({
        groupErrors: {},
        filesFromGroup: [],
        nbRunningActions: {
            group: 0,
            items: 0,
        },
        queuedActions: {
            group: [],
            items: [],
        },
        sortablejsObj: null,
        itemsIncrementInProgress: false,

        initDropzone() {
            _handleDropzone(this.$el, (e) => {
                this.upload(e.dataTransfer.files)
            })
        },

        initSortable() {
            const sortablejs = eval(window.livewireUploadHandlerParams.sortablejsVar)
            const that = this

            if (! sortablejs || ! $wire.sortable) {
                return
            }

            this.sortablejsObj = new sortablejs(this.$el, {
                draggable: '.luh__sort-draggable',
                handle: '.luh__sort-handle',
                animation: 150,
                onStart(e) {
                    _dropzoneDisabled = true
                },
                onEnd(e) {
                    const previousItem = e.item.previousSibling.previousSibling

                    if (previousItem !== null && previousItem.nodeValue === '[if ENDBLOCK]><![endif]') {
                        previousItem.before(e.item)
                    }

                    _dropzoneDisabled = false
                },
                store: {
                    set(sort) {
                        that.sortablejsObj.option('disabled', true)

                        that._waitItemsActions(async () => {
                            await $wire.sortItems(sort.toArray())
                            that.sortablejsObj.option('disabled', false)
                        })
                    }
                }
            })
        },

        itemHidden(itemId) {
            if (! $wire.autoSave && $wire.items[itemId].id !== null) {
                return false
            }

            return $wire.items[itemId].deleted
        },

        async upload(files) {
            if (this.itemsIncrementInProgress) {
                return
            }

            this.filesFromGroup = []
            this.itemsIncrementInProgress = true
            this.groupErrors = {}

            for (let file of files) {
                file = await _compressImage(file, $wire.compressorjsSettings)

                if (_validateFile(file, this.groupErrors, $wire)) {
                    this.filesFromGroup.push(file)
                }
            }

            this._waitItemsActions(async () => {
                this.sortablejsObj?.option('disabled', true)
                await $wire.incrementItems(this.filesFromGroup.length)
                this.sortablejsObj?.option('disabled', false)
                this.itemsIncrementInProgress = false
            })
        },

        _waitItemsActions(groupAction) {
            this._coordinateActions('group', groupAction)
        },

        async _coordinateActions(key, action) {
            const otherKey = (key === 'group' ? 'items' : 'group')

            if (this.nbRunningActions[otherKey] > 0) {
                this.queuedActions[key].unshift(action)
                return
            }

            this.nbRunningActions[key]++
            await action()
            this.nbRunningActions[key]--

            if (this.nbRunningActions[key] <= 0) {
                while (this.queuedActions[otherKey].length > 0) {
                    this._coordinateActions(otherKey, this.queuedActions[otherKey].pop())
                }
            }
        }
    }))

    // ALPINE COMPONENT FOR ITEM
    // =========================================================================

    Alpine.data('LivewireUploadHandlerItem', ($wire) => ({
        itemErrors: {},
        uploading: false,
        uploadingFileOriginalName: null,
        chunkIndex: 0,
        uploadedSizeByChunk: [],
        deleted: false,
        deleteTimer: null,
        deleteTimerInterval: null,

        init() {
            if ($wire.uploadFromGroupAtIndex !== null) {
                this.upload(this.filesFromGroup[$wire.uploadFromGroupAtIndex])
            }

            this.deleted = $wire.itemData.deleted ?? false
        },

        initDropzone() {
            _handleDropzone(this.$el, (e) => {
                this.upload(e.dataTransfer.files[0])
            })
        },

        getUploadProgress() {
            if (! $wire.uploadingFileSize) {
                return 0
            }

            return Math.round(this.uploadedSizeByChunk.reduce((a, b) => a + b, 0) * 100 / $wire.uploadingFileSize)
        },

        async upload(file) {
            this.itemErrors = {}

            try {
                if (! $wire.attachedToGroup) {
                    file = await _compressImage(file, $wire.compressorjsSettings)
                }

                if (! _validateFile(file, this.itemErrors, $wire)) {
                    return
                }

                this.uploading = true
                this.uploadingFileOriginalName = file.name
                this.chunkIndex = 0
                this.uploadedSizeByChunk = []
                this.deleted = false
                $wire.uploadingFileSize = file.size
                $wire.hasErrorOnUpload = false
                this._uploadChunk(file, 0)

            } catch (error) {
                this._errorOnUpload()
            }
        },

        cancelUpload() {
            this._waitGroupActions(() => {
                this.uploading = false
                $wire.itemData.deleted = ($wire.itemData.id === null)
                $wire.cancelUpload('chunkFile')
                return $wire.deleteUploadingFile()
            })
        },

        deleteUploadedFile() {
            this._waitGroupActions(() => {
                $wire.itemData.deleted = ($wire.itemData.id === null)
                return $wire.deleteUploadedFile()
            })
        },

        deleteSavedFile(delay = 3) {
            this.deleted = true

            if (! $wire.autoSave) {
                $wire.itemData.deleted = true
                return
            }

            const applyDelete = () => {
                this._waitGroupActions(() => {
                    $wire.itemData.deleted = true
                    return $wire.deleteSavedFile()
                })
            }

            if (delay <= 0) {
                applyDelete()
                return
            }

            this.deleteTimer = delay

            this.deleteTimerInterval = setInterval(() => {
                if (this.deleteTimer > 1) {
                    this.deleteTimer--
                } else {
                    clearInterval(this.deleteTimerInterval)
                    applyDelete()
                }
            }, 1000)
        },

        undeleteSavedFile() {
            clearInterval(this.deleteTimerInterval)
            this.deleted = false
            $wire.itemData.deleted = false
        },

        async _uploadChunk(originalFile, chunkStart) {
            if (! this.uploading) {
                return
            }

            this._waitGroupActions(async () => {
                const chunkEnd = Math.min(chunkStart + window.livewireUploadHandlerParams.chunkSize, originalFile.size)
                const chunkFile = originalFile.slice(chunkStart, chunkEnd, originalFile.type)
                chunkFile.name = originalFile.name

                await _waitBeforeNextUpload()

                $wire.upload('chunkFile', chunkFile, () => {
                    if ($wire.hasErrorOnUpload) {
                        this._errorOnUpload()
                        return
                    }

                    this.uploading &= chunkEnd < originalFile.size

                    if (this.uploading) {
                        this.chunkIndex++
                        this._uploadChunk(originalFile, chunkEnd)

                    } else if ($wire.onlyUpload) {
                        $wire.itemData.deleted = true
                    }
                }, () => {
                    this._errorOnUpload()
                }, (event) => {
                    this.uploadedSizeByChunk[this.chunkIndex] = chunkFile.size * event.detail.progress / 100
                })
            })
        },

        _errorOnUpload() {
            if ($wire.attachedToGroup && $wire.itemData.id === null) {
                this.groupErrors[this.uploadingFileOriginalName] = window.livewireUploadHandlerParams.uploadErrorMessage
            } else {
                this.itemErrors[this.uploadingFileOriginalName] = window.livewireUploadHandlerParams.uploadErrorMessage
            }

            this.cancelUpload()
        },

        _waitGroupActions(itemAction) {
            if (! $wire.attachedToGroup) {
                itemAction()
                return
            }

            this._coordinateActions('items', itemAction)
        }
    }))
})

/******/ })()
;
//# sourceMappingURL=scripts.13505c6331fd440b3c2057d57a6810ec.js.map