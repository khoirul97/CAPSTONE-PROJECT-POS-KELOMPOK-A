<template>
  <div :class="{fullscreen:fullscreen}" class="tinymce-container editor-container">
    <textarea :id="tinymceId" class="tinymce-textarea" />
  </div>
</template>

<script>
import plugins from './plugins';
import toolbar from './toolbar';

export default {
  name: 'Tinymce',
  props: {
    id: {
      type: String,
      default: function() {
        return 'vue-tinymce-' + +new Date() + ((Math.random() * 1000).toFixed(0) + '');
      },
    },
    value: {
      type: String,
      default: '',
    },
    toolbar: {
      type: Array,
      required: false,
      default() {
        return [];
      },
    },
    menubar: {
      type: String,
      default: 'file edit insert view format table',
    },
    height: {
      type: Number,
      required: false,
      default: 360,
    },
  },
  data() {
    return {
      hasChange: false,
      hasInit: false,
      tinymceId: this.id,
      fullscreen: false,
      languageTypeList: {
        'en': 'en',
        'zh': 'zh_CN',
      },
    };
  },
  computed: {
    language() {
      return this.languageTypeList[this.$store.getters.language];
    },
  },
  watch: {
    value(val) {
      if (!this.hasChange && this.hasInit) {
        this.$nextTick(() =>
          window.tinymce.get(this.tinymceId).setContent(val || ''));
      }
    },
    language() {
      this.destroyTinymce();
      this.$nextTick(() => this.initTinymce());
    },
  },
  mounted() {
    this.initTinymce();
  },
  activated() {
    this.initTinymce();
  },
  deactivated() {
    this.destroyTinymce();
  },
  destroyed() {
    this.destroyTinymce();
  },
  methods: {
    initTinymce() {
      const _this = this;
      window.tinymce.init({
        language: this.language,
        selector: `#${this.tinymceId}`,
        height: this.height,
        body_class: 'panel-body ',
        object_resizing: true,
        table_resize_bars: true,
        toolbar: this.toolbar.length > 0 ? this.toolbar : toolbar,
        menubar: this.menubar,
        plugins: plugins,
        contextmenu: 'link image inserttable | cell row column deletetable | tableborder', // 🔥 tambahkan tableborder
        end_container_on_empty_block: true,
        powerpaste_word_import: 'clean',
        code_dialog_height: 450,
        code_dialog_width: 1000,
        advlist_bullet_styles: 'square',
        advlist_number_styles: 'default',
        imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
        default_link_target: '_blank',
        link_title: false,
        nonbreaking_force_tab: true,
        // Tambahkan konfigurasi untuk tabel
        table_default_attributes: {
          border: '0',
          width: '100%',
        },
        table_default_styles: {
          borderCollapse: 'collapse',
          width: '100%',
        },
        table_style_by_css: false, // Gunakan atribut HTML untuk kompatibilitas TCPDF
        init_instance_callback: editor => {
          if (_this.value) {
            editor.setContent(_this.value);
          }
          _this.hasInit = true;
          editor.on('NodeChange Change KeyUp SetContent', () => {
            this.hasChange = true;
            this.$emit('input', editor.getContent());
          });
        },
        setup(editor) {
          editor.on('FullscreenStateChanged', (e) => {
            _this.fullscreen = e.state;
          });

          // 🔥 Logic toggle dipisah supaya dipakai bersama oleh tombol toolbar & context menu
          const toggleTableBorder = () => {
            const table = editor.dom.getParent(editor.selection.getNode(), 'table');
            if (!table) {
              editor.notificationManager.open({
                text: 'Letakkan kursor di dalam tabel terlebih dahulu',
                type: 'warning',
                timeout: 2000,
              });
              return;
            }

            const hasBorder = editor.dom.getAttrib(table, 'border') && editor.dom.getAttrib(table, 'border') !== '0';

            if (hasBorder) {
              editor.dom.setAttrib(table, 'border', '0');
              editor.dom.setStyle(table, 'border', 'none');
              editor.dom.select('td,th', table).forEach((cell) => {
                editor.dom.setStyle(cell, 'border', 'none');
              });
            } else {
              editor.dom.setAttrib(table, 'border', '1');
              editor.dom.setStyle(table, 'border', '1px solid #000000');
              editor.dom.select('td,th', table).forEach((cell) => {
                editor.dom.setStyle(cell, 'border', '1px solid #000000');
              });
            }

            editor.fire('change');
            editor.fire('SetContent');
          };

          // Tombol toolbar (sudah ada, sekarang pakai function bersama + indikator visual)
          editor.addButton('tableborder', {
            icon: 'table',
            tooltip: 'Tampilkan/Sembunyikan Border Tabel',
            onclick: toggleTableBorder,
            onPostRender: function() {
              const btnApi = this;

              const updateState = () => {
                const table = editor.dom.getParent(editor.selection.getNode(), 'table');
                const btnEl = btnApi.getEl();

                if (!table) {
                  btnApi.active(false);
                  if (btnEl) {
                    editor.dom.removeClass(btnEl, 'table-border-active');
                  }
                  return;
                }

                const hasBorder = editor.dom.getAttrib(table, 'border') && editor.dom.getAttrib(table, 'border') !== '0';
                btnApi.active(hasBorder);

                // 🔥 Class tambahan untuk pewarnaan custom (lihat <style> di bawah)
                if (btnEl) {
                  if (hasBorder) {
                    editor.dom.addClass(btnEl, 'table-border-active');
                  } else {
                    editor.dom.removeClass(btnEl, 'table-border-active');
                  }
                }
              };

              editor.on('NodeChange SetContent', updateState);
            },
          });

          // 🔥 Context menu: muncul saat klik-kanan di dalam tabel
          editor.addMenuItem('tableborder', {
            text: 'Tabel Border',
            icon: 'table',
            context: 'table',
            onclick: toggleTableBorder,
          });
        },
      });
    },
    destroyTinymce() {
      const tinymce = window.tinymce.get(this.tinymceId);
      if (this.fullscreen) {
        tinymce.execCommand('mceFullScreen');
      }

      if (tinymce) {
        tinymce.destroy();
      }
    },
    setContent(value) {
      window.tinymce.get(this.tinymceId).setContent(value);
    },
    getContent() {
      window.tinymce.get(this.tinymceId).getContent();
    },
    imageSuccessCBK(arr) {
      const _this = this;
      arr.forEach(v => {
        window.tinymce.get(_this.tinymceId).insertContent(`<img class="wscnph" src="${v.url}" >`);
      });
    },
  },
};
</script>

<style scoped>
.tinymce-container {
  position: relative;
  line-height: normal;
}
.tinymce-container>>>.mce-fullscreen {
  z-index: 10000;
}
.tinymce-textarea {
  visibility: hidden;
  z-index: -1;
}
.editor-custom-btn-container {
  position: absolute;
  right: 4px;
  top: 4px;
}
.fullscreen .editor-custom-btn-container {
  z-index: 10000;
  position: fixed;
}
.editor-upload-btn {
  display: inline-block;
}
</style>

<style scoped>
.tinymce-container {
  position: relative;
  line-height: normal;
}
.tinymce-container>>>.mce-fullscreen {
  z-index: 10000;
}
.tinymce-textarea {
  visibility: hidden;
  z-index: -1;
}
.editor-custom-btn-container {
  position: absolute;
  right: 4px;
  top: 4px;
}
.fullscreen .editor-custom-btn-container {
  z-index: 10000;
  position: fixed;
}
.editor-upload-btn {
  display: inline-block;
}

/* 🔥 Indikator visual saat border tabel aktif */
.tinymce-container>>>.mce-btn.table-border-active {
  background-color: rgba(37, 99, 235, 0.12);
  border-color: #2563eb;
}
.tinymce-container>>>.mce-btn.table-border-active i.mce-ico {
  color: #2563eb;
}
</style>
