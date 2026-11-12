(() => {
  document.addEventListener("alpine:init", () => {
    let _nextUploadTime = 0;
    let _dropzoneDisabled = false;
    function _handleDropzone(container, dropCallback) {
      container.addEventListener("dragover", (e) => {
        e.preventDefault();
        if (!_dropzoneDisabled) {
          container.classList.add("luh__dropzone-dragging");
        }
      });
      container.addEventListener("dragleave", (e) => {
        container.classList.remove("luh__dropzone-dragging");
      });
      container.addEventListener("drop", (e) => {
        e.preventDefault();
        container.classList.remove("luh__dropzone-dragging");
        if (!_dropzoneDisabled) {
          dropCallback(e);
        }
      });
    }
    function _getImageDimensions(file2) {
      return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
          resolve({ width: img.naturalWidth, height: img.naturalHeight });
          URL.revokeObjectURL(img.src);
        };
        img.onerror = () => {
          resolve(null);
          URL.revokeObjectURL(img.src);
        };
        img.src = URL.createObjectURL(file2);
      });
    }
    async function _validateFile(file2, errors, $wire2) {
      if ($wire2.acceptsMimeTypes.length > 0 && !$wire2.acceptsMimeTypes.includes(file2.type)) {
        errors[file2.name] = window.livewireUploadHandlerParams.invalidFileTypeErrorMessage;
        return false;
      }
      if ($wire2.maxFileSize > 0 && file2.size > $wire2.maxFileSize) {
        errors[file2.name] = window.livewireUploadHandlerParams.fileTooLoudErrorMessage;
        return false;
      }
      const hasMinWidth = ($wire2.minWidth ?? 0) > 0;
      const hasMaxWidth = ($wire2.maxWidth ?? 0) > 0;
      const hasMinHeight = ($wire2.minHeight ?? 0) > 0;
      const hasMaxHeight = ($wire2.maxHeight ?? 0) > 0;
      if (file2.type.startsWith("image/") && (hasMinWidth || hasMaxWidth || hasMinHeight || hasMaxHeight)) {
        const dims = await _getImageDimensions(file2);
        if (dims) {
          if (hasMinWidth && dims.width < $wire2.minWidth) {
            errors[file2.name] = window.livewireUploadHandlerParams.imageTooSmallWidthErrorMessage.replace(":min", $wire2.minWidth);
            return false;
          }
          if (hasMaxWidth && dims.width > $wire2.maxWidth) {
            errors[file2.name] = window.livewireUploadHandlerParams.imageTooLargeWidthErrorMessage.replace(":max", $wire2.maxWidth);
            return false;
          }
          if (hasMinHeight && dims.height < $wire2.minHeight) {
            errors[file2.name] = window.livewireUploadHandlerParams.imageTooSmallHeightErrorMessage.replace(":min", $wire2.minHeight);
            return false;
          }
          if (hasMaxHeight && dims.height > $wire2.maxHeight) {
            errors[file2.name] = window.livewireUploadHandlerParams.imageTooLargeHeightErrorMessage.replace(":max", $wire2.maxHeight);
            return false;
          }
        }
      }
      return true;
    }
    function _waitBeforeNextUpload() {
      _nextUploadTime = Math.max(_nextUploadTime, Date.now());
      const timeToWait = _nextUploadTime - Date.now();
      _nextUploadTime += 1050;
      return new Promise((r) => setTimeout(r, timeToWait));
    }
    async function _runAction(action) {
      try {
        await action();
      } catch (error) {
        console.error(error);
      }
    }
    function _compressImage(file, settings) {
      const compressorjs = eval(window.livewireUploadHandlerParams.compressorjsVar);
      if (!compressorjs || !file.type.startsWith("image/")) {
        return file;
      }
      return new Promise((resolve, reject) => {
        new compressorjs(file, {
          ...settings,
          success: resolve,
          error: reject
        });
      });
    }
    const _itemsComponentsIds = /* @__PURE__ */ new Set();
    const _itemsActiveMessages = /* @__PURE__ */ new Map();
    const _heldActions = /* @__PURE__ */ new Set();
    function _hasBusyBoundItem(component) {
      let hasBusyBoundItem = false;
      component.getDeepChildrenWithBindings?.((child) => {
        hasBusyBoundItem ||= _itemsActiveMessages.has(child.id);
      });
      return hasBusyBoundItem;
    }
    function _releaseHeldActions() {
      _heldActions.forEach((action) => {
        if (!_hasBusyBoundItem(action.component)) {
          _heldActions.delete(action);
          action.fire();
        }
      });
    }
    if (typeof Livewire.interceptAction === "function" && typeof Livewire.interceptMessage === "function") {
      Livewire.interceptMessage(({ message, onFinish }) => {
        const componentId = message.component.id;
        if (!_itemsComponentsIds.has(componentId)) {
          return;
        }
        _itemsActiveMessages.set(componentId, (_itemsActiveMessages.get(componentId) ?? 0) + 1);
        onFinish(() => {
          const nbActiveMessages = _itemsActiveMessages.get(componentId) - 1;
          if (nbActiveMessages > 0) {
            _itemsActiveMessages.set(componentId, nbActiveMessages);
          } else {
            _itemsActiveMessages.delete(componentId);
          }
          _releaseHeldActions();
        });
      });
      Livewire.interceptAction(({ action }) => {
        if (!action.isDeferred() && _hasBusyBoundItem(action.component)) {
          action.defer();
          _heldActions.add(action);
        }
      });
    }
    Alpine.data("LivewireUploadHandlerGroup", ($wire) => ({
      groupErrors: {},
      filesFromGroup: [],
      nbRunningActions: {
        group: 0,
        items: 0
      },
      queuedActions: {
        group: [],
        items: []
      },
      sortablejsObj: null,
      itemsIncrementInProgress: false,
      init() {
        $wire.watch("items", (value, oldValue) => {
          if (value === null) {
            $wire.items = oldValue;
          }
        });
      },
      initDropzone() {
        _handleDropzone(this.$el, (e) => {
          this.upload(e.dataTransfer.files);
        });
      },
      initSortable() {
        const sortablejs = eval(window.livewireUploadHandlerParams.sortablejsVar);
        const that = this;
        if (!sortablejs || !$wire.sortable) {
          return;
        }
        this.sortablejsObj = new sortablejs(this.$el, {
          draggable: ".luh__sort-draggable",
          handle: ".luh__sort-handle",
          animation: 150,
          onStart(e) {
            _dropzoneDisabled = true;
          },
          onEnd(e) {
            const previousItem = e.item.previousSibling.previousSibling;
            if (previousItem !== null && previousItem.nodeValue === "[if ENDBLOCK]><![endif]") {
              previousItem.before(e.item);
            }
            _dropzoneDisabled = false;
          },
          store: {
            set(sort) {
              that.sortablejsObj.option("disabled", true);
              that._waitItemsActions(async () => {
                try {
                  await $wire.sortItems(sort.toArray());
                } finally {
                  that.sortablejsObj.option("disabled", false);
                }
              });
            }
          }
        });
      },
      itemHidden(itemId) {
        if ($wire.items === null || !$wire.items[itemId]) {
          return false;
        }
        if (!$wire.autoSave && $wire.items[itemId].id !== null) {
          return false;
        }
        return $wire.items[itemId].deleted;
      },
      visibleItemsNumber() {
        return Object.keys($wire.items ?? {}).filter((itemId) => !this.itemHidden(itemId)).length;
      },
      maxFilesNumberReached() {
        if ($wire.maxFilesNumber <= 0) {
          return false;
        }
        return this.visibleItemsNumber() >= $wire.maxFilesNumber;
      },
      async upload(files) {
        if (this.itemsIncrementInProgress) {
          return;
        }
        this.filesFromGroup = [];
        this.itemsIncrementInProgress = true;
        this.groupErrors = {};
        for (let file2 of files) {
          file2 = await _compressImage(file2, $wire.compressorjsSettings);
          if (await _validateFile(file2, this.groupErrors, $wire)) {
            this.filesFromGroup.push(file2);
          }
        }
        const nbFilesFromGroup = this.filesFromGroup.length;
        let nbFilesToAdd = nbFilesFromGroup;
        if ($wire.maxFilesNumber > 0) {
          for (let i = $wire.maxFilesNumber - this.visibleItemsNumber(); i < nbFilesFromGroup; i++) {
            this.groupErrors[this.filesFromGroup[i].name] = window.livewireUploadHandlerParams.maxFilesNumberReachedMessage;
            nbFilesToAdd--;
          }
        }
        this._waitItemsActions(async () => {
          this.sortablejsObj?.option("disabled", true);
          try {
            await $wire.incrementItems(nbFilesToAdd);
          } finally {
            this.sortablejsObj?.option("disabled", false);
            this.itemsIncrementInProgress = false;
          }
        });
      },
      _waitItemsActions(groupAction) {
        this._coordinateActions("group", groupAction);
      },
      async _coordinateActions(key, action) {
        const otherKey = key === "group" ? "items" : "group";
        if (this.nbRunningActions[otherKey] > 0) {
          this.queuedActions[key].unshift(action);
          return;
        }
        this.nbRunningActions[key]++;
        await _runAction(action);
        this.nbRunningActions[key]--;
        if (this.nbRunningActions[key] <= 0) {
          while (this.queuedActions[otherKey].length > 0) {
            this._coordinateActions(otherKey, this.queuedActions[otherKey].pop());
          }
        }
      }
    }));
    Alpine.data("LivewireUploadHandlerItem", ($wire2) => ({
      itemErrors: {},
      uploading: false,
      uploadingFileOriginalName: null,
      chunkIndex: 0,
      uploadedSizeByChunk: [],
      deleted: false,
      deleteTimer: null,
      deleteTimerInterval: null,
      init() {
        _itemsComponentsIds.add($wire2.$id);
        $wire2.watch("itemData", (value, oldValue) => {
          if (value === null) {
            $wire2.itemData = oldValue;
          }
        });
        if ($wire2.uploadFromGroupAtIndex !== null) {
          this.upload(this.filesFromGroup[$wire2.uploadFromGroupAtIndex]);
        }
        this.deleted = $wire2.itemData.deleted ?? false;
      },
      initDropzone() {
        _handleDropzone(this.$el, (e) => {
          this.upload(e.dataTransfer.files[0]);
        });
      },
      hasSavedFile() {
        return $wire2.savedFileDisk !== null && $wire2.savedFilePath !== null;
      },
      uploadProgressValue() {
        if (!$wire2.uploadingFileSize) {
          return 0;
        }
        return Math.round(this.uploadedSizeByChunk.reduce((a, b) => a + b, 0) * 100 / $wire2.uploadingFileSize);
      },
      async upload(file2) {
        this.itemErrors = {};
        try {
          if (!$wire2.attachedToGroup) {
            file2 = await _compressImage(file2, $wire2.compressorjsSettings);
          }
          if (!await _validateFile(file2, this.itemErrors, $wire2)) {
            return;
          }
          this.uploading = true;
          this.uploadingFileOriginalName = file2.name;
          this.chunkIndex = 0;
          this.uploadedSizeByChunk = [];
          this.deleted = false;
          $wire2.uploadingFileSize = file2.size;
          $wire2.hasErrorOnUpload = false;
          this._uploadChunk(file2, 0);
        } catch (error) {
          this._errorOnUpload();
        }
      },
      cancelUpload() {
        this._waitGroupActions(() => {
          this.uploading = false;
          $wire2.itemData.deleted = !this.hasSavedFile();
          $wire2.cancelUpload("chunkFile");
          return $wire2.deleteUploadingFile();
        });
      },
      deleteUploadedFile() {
        this._waitGroupActions(() => {
          $wire2.itemData.deleted = !this.hasSavedFile();
          return $wire2.deleteUploadedFile();
        });
      },
      deleteSavedFile(delay = 3) {
        this.deleted = true;
        if (!$wire2.autoSave) {
          $wire2.itemData.deleted = true;
          return;
        }
        const applyDelete = () => {
          this._waitGroupActions(() => {
            $wire2.itemData.deleted = true;
            return $wire2.deleteSavedFile();
          });
        };
        if (delay <= 0) {
          applyDelete();
          return;
        }
        this.deleteTimer = delay;
        this.deleteTimerInterval = setInterval(() => {
          if (this.deleteTimer > 1) {
            this.deleteTimer--;
          } else {
            clearInterval(this.deleteTimerInterval);
            applyDelete();
          }
        }, 1e3);
      },
      undeleteSavedFile() {
        clearInterval(this.deleteTimerInterval);
        this.deleted = false;
        $wire2.itemData.deleted = false;
      },
      async _uploadChunk(originalFile, chunkStart) {
        if (!this.uploading) {
          return;
        }
        this._waitGroupActions(async () => {
          const chunkEnd = Math.min(chunkStart + window.livewireUploadHandlerParams.chunkSize, originalFile.size);
          const chunkFile = originalFile.slice(chunkStart, chunkEnd, originalFile.type);
          chunkFile.name = originalFile.name;
          await _waitBeforeNextUpload();
          if (!this.uploading) {
            return;
          }
          $wire2.upload("chunkFile", chunkFile, () => {
            if ($wire2.hasErrorOnUpload) {
              this._errorOnUpload();
              return;
            }
            this.uploading &= chunkEnd < originalFile.size;
            if (this.uploading) {
              this.chunkIndex++;
              this._uploadChunk(originalFile, chunkEnd);
            } else if ($wire2.onlyUpload) {
              $wire2.itemData.deleted = true;
            }
          }, () => {
            this._errorOnUpload();
          }, (event) => {
            this.uploadedSizeByChunk[this.chunkIndex] = chunkFile.size * event.detail.progress / 100;
          });
        });
      },
      _errorOnUpload() {
        if ($wire2.attachedToGroup && !this.hasSavedFile()) {
          this.groupErrors[this.uploadingFileOriginalName] = window.livewireUploadHandlerParams.uploadErrorMessage;
        } else {
          this.itemErrors[this.uploadingFileOriginalName] = window.livewireUploadHandlerParams.uploadErrorMessage;
        }
        this.cancelUpload();
      },
      _waitGroupActions(itemAction) {
        if (!$wire2.attachedToGroup) {
          _runAction(itemAction);
          return;
        }
        this._coordinateActions("items", itemAction);
      }
    }));
  });
})();
