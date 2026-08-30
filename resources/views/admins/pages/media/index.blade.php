@extends(request('picker') ? 'admins.layouts.iframe' : 'admins.layouts.master')

@section('title', 'Media Manager')

@section('styles')
<style>
/* ═══════════════════════════════════════════════════
   MEDIA MANAGER — COMPLETE STYLESHEET
   ═══════════════════════════════════════════════════ */
:root {
    --mm-bg:          #f1f5f9;
    --mm-surface:     #ffffff;
    --mm-border:      #e2e8f0;
    --mm-border2:     #cbd5e1;
    --mm-text:        #1e293b;
    --mm-text2:       #475569;
    --mm-text3:       #94a3b8;
    --mm-primary:     #3b82f6;
    --mm-primary-h:   #2563eb;
    --mm-danger:      #ef4444;
    --mm-danger-h:    #dc2626;
    --mm-success:     #10b981;
    --mm-warning:     #f59e0b;
    --mm-folder:      #f59e0b;
    --mm-shadow:      0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
    --mm-shadow-md:   0 4px 12px rgba(0,0,0,.1);
    --mm-shadow-lg:   0 20px 40px rgba(0,0,0,.15);
    --mm-radius:      8px;
    --mm-radius-sm:   5px;
    --mm-sidebar:     260px;
    --mm-panel:       320px;
    --mm-toolbar:     52px;
    --mm-breadcrumb:  42px;
    --mm-transition:  .15s ease;
}

/* ── Layout ── */
.mm-root {
    display: flex;
    height: calc(100vh - 60px);
    background: var(--mm-bg);
    font-family: 'Inter', system-ui, sans-serif;
    overflow: hidden;
}

/* ── Sidebar (Folder Tree) ── */
.mm-sidebar {
    width: var(--mm-sidebar);
    min-width: var(--mm-sidebar);
    background: var(--mm-surface);
    border-right: 1px solid var(--mm-border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 10;
}
.mm-sidebar-head {
    padding: 14px 16px;
    border-bottom: 1px solid var(--mm-border);
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 13px;
    color: var(--mm-text);
    background: var(--mm-bg);
}
.mm-sidebar-head i { font-size: 16px; color: var(--mm-primary); }
.mm-tree { flex: 1; overflow-y: auto; padding: 8px 0; }
.mm-tree-item {
    display: flex; align-items: center;
    gap: 4px; padding: 5px 8px;
    cursor: pointer; border-radius: var(--mm-radius-sm);
    margin: 0 6px; font-size: 13px; color: var(--mm-text2);
    user-select: none; transition: all var(--mm-transition);
}
.mm-tree-item:hover { background: var(--mm-bg); color: var(--mm-text); }
.mm-tree-item.active { background: #eff6ff; color: var(--mm-primary); font-weight: 600; }
.mm-tree-item .mm-ti-arrow {
    width: 16px; height: 16px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; color: var(--mm-text3);
    transition: transform var(--mm-transition);
}
.mm-tree-item .mm-ti-arrow.open { transform: rotate(90deg); }
.mm-tree-item .mm-ti-icon { font-size: 14px; flex-shrink: 0; color: var(--mm-folder); }
.mm-tree-item .mm-ti-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mm-tree-children { display: none; }
.mm-tree-children.open { display: block; }

/* ── Main area ── */
.mm-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── Breadcrumb ── */
.mm-breadcrumb {
    height: var(--mm-breadcrumb);
    background: var(--mm-surface);
    border-bottom: 1px solid var(--mm-border);
    display: flex;
    align-items: center;
    padding: 0 16px;
    gap: 4px;
    font-size: 13px;
}
.mm-bc-item {
    color: var(--mm-text2);
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 3px;
    transition: color var(--mm-transition);
}
.mm-bc-item:hover { color: var(--mm-primary); }
.mm-bc-item.active { color: var(--mm-text); font-weight: 600; cursor: default; }
.mm-bc-sep { color: var(--mm-text3); font-size: 11px; }

/* ── Toolbar ── */
.mm-toolbar {
    height: var(--mm-toolbar);
    background: var(--mm-surface);
    border-bottom: 1px solid var(--mm-border);
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 14px;
    flex-wrap: nowrap;
}
.mm-toolbar-l { display: flex; gap: 6px; align-items: center; flex: 1; }
.mm-toolbar-r { display: flex; gap: 6px; align-items: center; }

/* ── Buttons ── */
.mm-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; font-size: 13px; font-weight: 500;
    border-radius: var(--mm-radius-sm); border: 1px solid var(--mm-border2);
    background: var(--mm-surface); color: var(--mm-text2);
    cursor: pointer; transition: all var(--mm-transition); white-space: nowrap;
}
.mm-btn:hover { background: var(--mm-bg); border-color: var(--mm-primary); color: var(--mm-primary); }
.mm-btn-primary { background: var(--mm-primary); color: #fff; border-color: var(--mm-primary); }
.mm-btn-primary:hover { background: var(--mm-primary-h); border-color: var(--mm-primary-h); color: #fff; }
.mm-btn-danger { background: var(--mm-danger); color: #fff; border-color: var(--mm-danger); }
.mm-btn-danger:hover { background: var(--mm-danger-h); border-color: var(--mm-danger-h); color: #fff; }
.mm-btn-icon { padding: 6px 8px; }
.mm-btn:disabled { opacity: .5; cursor: not-allowed; }

/* ── Search ── */
.mm-search-wrap {
    display: flex; align-items: center;
    background: var(--mm-bg); border: 1px solid var(--mm-border2);
    border-radius: var(--mm-radius-sm);
    padding: 0 10px; gap: 6px;
}
.mm-search-wrap:focus-within { border-color: var(--mm-primary); box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.mm-search-wrap i { color: var(--mm-text3); font-size: 14px; }
.mm-search { background: none; border: none; outline: none; font-size: 13px; color: var(--mm-text); padding: 5px 0; width: 180px; }

/* ── Select ── */
.mm-select {
    border: 1px solid var(--mm-border2); border-radius: var(--mm-radius-sm);
    padding: 5px 8px; font-size: 13px; color: var(--mm-text2);
    background: var(--mm-surface); cursor: pointer; outline: none;
}
.mm-select:focus { border-color: var(--mm-primary); }

/* ── View toggle ── */
.mm-view-toggle { display: flex; border: 1px solid var(--mm-border2); border-radius: var(--mm-radius-sm); overflow: hidden; }
.mm-view-btn { padding: 5px 9px; cursor: pointer; color: var(--mm-text3); border: none; background: none; font-size: 14px; }
.mm-view-btn.active { background: var(--mm-primary); color: #fff; }
.mm-view-btn:not(.active):hover { background: var(--mm-bg); }

/* ── Content area ── */
.mm-content-wrap {
    flex: 1;
    display: flex;
    overflow: hidden;
}
.mm-content {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

/* ── Section label ── */
.mm-section-label {
    font-size: 11px; font-weight: 600; color: var(--mm-text3);
    text-transform: uppercase; letter-spacing: .7px;
    margin: 0 0 8px 2px;
}

/* ── Folder list ── */
.mm-folders-row {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-bottom: 20px;
}
.mm-folder-item {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 14px; border: 1px solid var(--mm-border);
    border-radius: var(--mm-radius); background: var(--mm-surface);
    cursor: pointer; font-size: 13px; color: var(--mm-text2);
    transition: all var(--mm-transition);
    user-select: none; position: relative;
}
.mm-folder-item:hover { border-color: var(--mm-folder); color: var(--mm-text); box-shadow: var(--mm-shadow); }
.mm-folder-item i.mdi-folder { color: var(--mm-folder); font-size: 18px; }
.mm-folder-item .mm-folder-count {
    font-size: 11px; color: var(--mm-text3); margin-left: 2px;
}
.mm-folder-item .mm-folder-actions {
    display: none; position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
    gap: 2px;
}
.mm-folder-item:hover .mm-folder-actions { display: flex; }
.mm-folder-action-btn {
    width: 22px; height: 22px; border-radius: 3px; display: flex;
    align-items: center; justify-content: center;
    font-size: 12px; color: var(--mm-text3); cursor: pointer;
    transition: all var(--mm-transition);
}
.mm-folder-action-btn:hover { background: var(--mm-bg); color: var(--mm-text); }
.mm-folder-action-btn.danger:hover { background: #fee2e2; color: var(--mm-danger); }

/* ── Grid view ── */
.mm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px;
}
.mm-file-item {
    border: 2px solid transparent; border-radius: var(--mm-radius);
    background: var(--mm-surface); cursor: pointer;
    transition: all var(--mm-transition); overflow: hidden;
    position: relative; user-select: none;
}
.mm-file-item:hover { border-color: var(--mm-border2); box-shadow: var(--mm-shadow); }
.mm-file-item.selected { border-color: var(--mm-primary); background: #eff6ff; }
.mm-file-item.missing { opacity: .5; }
.mm-file-thumb {
    width: 100%; aspect-ratio: 1 / 1;
    display: flex; align-items: center; justify-content: center;
    background: var(--mm-bg); overflow: hidden;
}
.mm-file-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .2s ease;
}
.mm-file-item:hover .mm-file-thumb img { transform: scale(1.05); }
.mm-file-thumb i { font-size: 36px; color: var(--mm-text3); }
.mm-file-info { padding: 6px 8px; }
.mm-file-name {
    font-size: 11.5px; font-weight: 500; color: var(--mm-text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1.3;
}
.mm-file-meta { font-size: 10.5px; color: var(--mm-text3); margin-top: 2px; }
.mm-file-chk {
    position: absolute; top: 6px; left: 6px;
    width: 18px; height: 18px; border-radius: 50%;
    background: var(--mm-primary); display: flex;
    align-items: center; justify-content: center;
    opacity: 0; transition: opacity var(--mm-transition);
}
.mm-file-chk i { font-size: 11px; color: #fff; }
.mm-file-item.selected .mm-file-chk { opacity: 1; }
.mm-file-item:hover .mm-file-chk { opacity: .4; }
.mm-file-item.selected:hover .mm-file-chk { opacity: 1; }
.mm-file-badge {
    position: absolute; top: 6px; right: 6px;
    background: rgba(0,0,0,.5); color: #fff;
    font-size: 9px; padding: 1px 4px;
    border-radius: 3px; font-weight: 600; text-transform: uppercase;
}

/* ── List view ── */
.mm-list {
    border: 1px solid var(--mm-border);
    border-radius: var(--mm-radius);
    background: var(--mm-surface);
    overflow: hidden;
}
.mm-list-head {
    display: grid; grid-template-columns: 32px 44px 1fr 80px 120px 90px 100px;
    padding: 8px 14px; font-size: 11px; font-weight: 600;
    color: var(--mm-text3); text-transform: uppercase; letter-spacing: .4px;
    background: var(--mm-bg); border-bottom: 1px solid var(--mm-border);
    cursor: pointer; user-select: none;
}
.mm-list-head span:hover { color: var(--mm-text); }
.mm-list-row {
    display: grid; grid-template-columns: 32px 44px 1fr 80px 120px 90px 100px;
    align-items: center; padding: 8px 14px; font-size: 13px;
    border-bottom: 1px solid var(--mm-border); color: var(--mm-text2);
    cursor: pointer; transition: background var(--mm-transition);
}
.mm-list-row:last-child { border-bottom: none; }
.mm-list-row:hover { background: #f8fafc; }
.mm-list-row.selected { background: #eff6ff; }
.mm-list-row .mm-list-name { font-weight: 500; color: var(--mm-text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mm-list-thumb { width: 32px; height: 32px; object-fit: cover; border-radius: 3px; }
.mm-list-icon { font-size: 22px; color: var(--mm-text3); }
.mm-list-chk { width: 16px; height: 16px; cursor: pointer; }

/* ── Empty state ── */
.mm-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 60px 20px; color: var(--mm-text3); text-align: center;
}
.mm-empty i { font-size: 56px; margin-bottom: 14px; }
.mm-empty h5 { font-size: 15px; color: var(--mm-text2); margin-bottom: 6px; }
.mm-empty p { font-size: 13px; }

/* ── Details Panel ── */
.mm-details {
    width: var(--mm-panel);
    min-width: var(--mm-panel);
    background: var(--mm-surface);
    border-left: 1px solid var(--mm-border);
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .2s ease;
    overflow: hidden;
}
.mm-details.open { transform: translateX(0); }
.mm-details-head {
    padding: 14px 16px; border-bottom: 1px solid var(--mm-border);
    display: flex; justify-content: space-between; align-items: center;
}
.mm-details-head h5 { font-size: 14px; font-weight: 700; color: var(--mm-text); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 220px; }
.mm-details-close { background: none; border: none; cursor: pointer; color: var(--mm-text3); font-size: 16px; }
.mm-details-close:hover { color: var(--mm-text); }
.mm-details-preview {
    background: var(--mm-bg); display: flex;
    align-items: center; justify-content: center;
    height: 200px; overflow: hidden;
}
.mm-details-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
.mm-details-preview i { font-size: 64px; color: var(--mm-text3); }
.mm-details-body { flex: 1; overflow-y: auto; padding: 14px 16px; }
.mm-details-actions {
    padding: 12px 16px; border-top: 1px solid var(--mm-border);
    display: flex; flex-wrap: wrap; gap: 6px;
}
.mm-field { margin-bottom: 14px; }
.mm-field label { display: block; font-size: 11px; font-weight: 600; color: var(--mm-text3); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
.mm-field input, .mm-field textarea {
    width: 100%; padding: 7px 10px; font-size: 13px; color: var(--mm-text);
    border: 1px solid var(--mm-border2); border-radius: var(--mm-radius-sm);
    background: var(--mm-bg); outline: none; transition: border var(--mm-transition);
    resize: vertical; font-family: inherit;
}
.mm-field input:focus, .mm-field textarea:focus { border-color: var(--mm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.mm-field-static { font-size: 13px; color: var(--mm-text2); word-break: break-all; }
.mm-field-copy {
    display: flex; gap: 6px; align-items: center;
}
.mm-field-copy input { flex: 1; font-size: 11.5px; }
.mm-copy-btn {
    padding: 6px 8px; border-radius: var(--mm-radius-sm);
    border: 1px solid var(--mm-border2); background: var(--mm-bg);
    cursor: pointer; font-size: 13px; color: var(--mm-text3);
    transition: all var(--mm-transition); white-space: nowrap;
}
.mm-copy-btn:hover { border-color: var(--mm-primary); color: var(--mm-primary); }
.mm-meta-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--mm-border); font-size: 12px; }
.mm-meta-row:last-child { border-bottom: none; }
.mm-meta-label { color: var(--mm-text3); }
.mm-meta-value { color: var(--mm-text2); font-weight: 500; text-align: right; max-width: 160px; word-break: break-all; }

/* ── Bulk bar ── */
.mm-bulk-bar {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: var(--mm-text); color: #fff;
    padding: 12px 24px; display: none; gap: 12px;
    align-items: center; justify-content: space-between;
    z-index: 100; border-top: 2px solid var(--mm-primary);
    animation: slideUp .2s ease;
}
.mm-bulk-bar.show { display: flex; }
@keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
.mm-bulk-info { font-size: 14px; font-weight: 600; }
.mm-bulk-actions { display: flex; gap: 8px; }

/* ── Upload Modal ── */
.mm-modal-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,.6);
    z-index: 2000; display: none; align-items: center;
    justify-content: center; backdrop-filter: blur(3px);
}
.mm-modal-overlay.show { display: flex; }
.mm-modal {
    background: var(--mm-surface); border-radius: 12px;
    width: 560px; max-width: 90vw; max-height: 90vh;
    display: flex; flex-direction: column;
    box-shadow: var(--mm-shadow-lg); overflow: hidden;
    animation: zoomIn .18s ease;
}
@keyframes zoomIn { from { transform: scale(.92); opacity:0; } to { transform: scale(1); opacity: 1; } }
.mm-modal-head {
    padding: 16px 20px; border-bottom: 1px solid var(--mm-border);
    display: flex; justify-content: space-between; align-items: center;
}
.mm-modal-head h5 { margin: 0; font-size: 15px; font-weight: 700; color: var(--mm-text); }
.mm-modal-close { background: none; border: none; font-size: 20px; color: var(--mm-text3); cursor: pointer; }
.mm-modal-close:hover { color: var(--mm-text); }
.mm-modal-body { padding: 20px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; }
.mm-modal-field label { display: block; font-size: 12px; font-weight: 600; color: var(--mm-text3); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 7px; }
.mm-modal-field select { width: 100%; padding: 9px 12px; border: 1px solid var(--mm-border2); border-radius: var(--mm-radius-sm); font-size: 13.5px; color: var(--mm-text); background: var(--mm-bg); outline: none; cursor: pointer; }
.mm-modal-field select:focus { border-color: var(--mm-primary); box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.mm-dropzone {
    border: 2px dashed var(--mm-border2); border-radius: var(--mm-radius);
    background: var(--mm-bg); padding: 36px 20px; text-align: center;
    cursor: pointer; transition: all .2s;
}
.mm-dropzone.drag { border-color: var(--mm-primary); background: #eff6ff; }
.mm-dropzone i { font-size: 44px; color: var(--mm-text3); display: block; margin-bottom: 10px; transition: all .2s; }
.mm-dropzone.drag i { color: var(--mm-primary); transform: scale(1.1); }
.mm-dropzone p { font-size: 14px; font-weight: 500; color: var(--mm-text2); margin: 0 0 5px; }
.mm-dropzone span { font-size: 12.5px; color: var(--mm-text3); }
.mm-upload-queue { max-height: 160px; overflow-y: auto; border: 1px solid var(--mm-border); border-radius: var(--mm-radius-sm); display: none; }
.mm-queue-item { display: flex; justify-content: space-between; align-items: center; padding: 7px 12px; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; }
.mm-queue-item:last-child { border-bottom: none; }
.mm-queue-item .q-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 68%; color: var(--mm-text2); }
.mm-queue-item .q-status { font-weight: 600; color: var(--mm-text3); }
.mm-queue-item.ok .q-status { color: var(--mm-success); }
.mm-queue-item.err .q-status { color: var(--mm-danger); }
.mm-modal-foot {
    padding: 12px 20px; border-top: 1px solid var(--mm-border);
    display: flex; align-items: center; gap: 12px;
}
.mm-progress-wrap { flex: 1; display: none; align-items: center; gap: 10px; }
.mm-progress-wrap.show { display: flex; }
.mm-progress-track { flex: 1; height: 5px; background: var(--mm-border); border-radius: 99px; overflow: hidden; }
.mm-progress-bar { height: 100%; background: var(--mm-primary); width: 0; transition: width .15s; border-radius: 99px; }
.mm-progress-text { font-size: 12px; font-weight: 600; color: var(--mm-text3); white-space: nowrap; }

/* ── Generic Modal (rename, new folder, move) ── */
.mm-prompt-modal {
    position: fixed; inset: 0; background: rgba(15,23,42,.5);
    z-index: 3000; display: none; align-items: center; justify-content: center;
}
.mm-prompt-modal.show { display: flex; }
.mm-prompt-box {
    background: var(--mm-surface); border-radius: var(--mm-radius);
    padding: 24px; width: 380px; max-width: 90vw;
    box-shadow: var(--mm-shadow-lg); animation: zoomIn .15s ease;
}
.mm-prompt-box h5 { font-size: 15px; font-weight: 700; color: var(--mm-text); margin: 0 0 14px; }
.mm-prompt-box input, .mm-prompt-box select {
    width: 100%; padding: 9px 12px; border: 1px solid var(--mm-border2);
    border-radius: var(--mm-radius-sm); font-size: 13.5px; color: var(--mm-text);
    outline: none; margin-bottom: 14px; font-family: inherit;
}
.mm-prompt-box input:focus { border-color: var(--mm-primary); box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.mm-prompt-actions { display: flex; gap: 8px; justify-content: flex-end; }

/* ── Toast ── */
.mm-toast {
    position: fixed; bottom: 20px; right: 20px; z-index: 9999;
    background: var(--mm-text); color: #fff;
    padding: 10px 18px; border-radius: var(--mm-radius);
    font-size: 13px; font-weight: 500;
    box-shadow: var(--mm-shadow-md);
    transform: translateY(100px); opacity: 0;
    transition: all .25s ease;
    display: flex; align-items: center; gap: 8px;
}
.mm-toast.show { transform: translateY(0); opacity: 1; }
.mm-toast.success { background: var(--mm-success); }
.mm-toast.error   { background: var(--mm-danger); }

/* ── Context Menu ── */
.mm-ctx {
    position: fixed; z-index: 5000;
    background: var(--mm-surface); border: 1px solid var(--mm-border);
    border-radius: var(--mm-radius); box-shadow: var(--mm-shadow-md);
    padding: 4px; min-width: 160px; display: none;
}
.mm-ctx.show { display: block; }
.mm-ctx-item {
    padding: 7px 12px; font-size: 13px; color: var(--mm-text2);
    cursor: pointer; border-radius: var(--mm-radius-sm);
    display: flex; align-items: center; gap: 8px;
    transition: background var(--mm-transition);
}
.mm-ctx-item:hover { background: var(--mm-bg); color: var(--mm-text); }
.mm-ctx-item.danger { color: var(--mm-danger); }
.mm-ctx-item.danger:hover { background: #fee2e2; }
.mm-ctx-sep { height: 1px; background: var(--mm-border); margin: 3px 0; }

/* ── Loading spinner ── */
.mm-spinner {
    display: none; justify-content: center; padding: 40px;
    align-items: center; gap: 10px; color: var(--mm-text3);
}
.mm-spinner.show { display: flex; }
@keyframes spin { to { transform: rotate(360deg); } }
.mm-spinner i { animation: spin 1s linear infinite; font-size: 22px; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .mm-sidebar { display: none; }
    .mm-details { display: none; }
}
</style>
@endsection

@section('content')

{{-- ══════════════ ROOT ══════════════ --}}
<div class="mm-root" id="mmRoot">

    {{-- SIDEBAR — Folder Tree --}}
    <div class="mm-sidebar">
        <div class="mm-sidebar-head">
            <i class="mdi mdi-folder-multiple"></i>
            Thư mục
        </div>
        <div class="mm-tree" id="mmTree">
            {{-- Built dynamically --}}
        </div>
    </div>

    {{-- MAIN --}}
    <div class="mm-main">

        {{-- Breadcrumb --}}
        <div class="mm-breadcrumb" id="mmBreadcrumb"></div>

        {{-- Toolbar --}}
        <div class="mm-toolbar">
            <div class="mm-toolbar-l">
                <button class="mm-btn mm-btn-primary" id="btnUpload">
                    <i class="mdi mdi-upload"></i> Tải lên
                </button>
                <button class="mm-btn" id="btnNewFolder">
                    <i class="mdi mdi-folder-plus-outline"></i> Thư mục mới
                </button>
                <div class="mm-search-wrap">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" class="mm-search" id="mmSearch" placeholder="Tìm kiếm..." autocomplete="off">
                </div>
                <select class="mm-select" id="mmSort" title="Sắp xếp">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="name_az">Tên A–Z</option>
                    <option value="name_za">Tên Z–A</option>
                    <option value="largest">Lớn nhất</option>
                    <option value="smallest">Nhỏ nhất</option>
                </select>
                <select class="mm-select" id="mmType" title="Loại">
                    <option value="all">Tất cả</option>
                    <option value="image">Ảnh</option>
                    <option value="video">Video</option>
                    <option value="document">Tài liệu</option>
                </select>
            </div>
            <div class="mm-toolbar-r">
                <button class="mm-btn mm-btn-icon" id="btnSync" title="Đồng bộ filesystem">
                    <i class="mdi mdi-sync"></i>
                </button>
                <div class="mm-view-toggle">
                    <button class="mm-view-btn active" id="btnGrid" title="Grid"><i class="mdi mdi-view-grid"></i></button>
                    <button class="mm-view-btn" id="btnList" title="List"><i class="mdi mdi-view-list"></i></button>
                </div>
            </div>
        </div>

        {{-- Content + Details Panel --}}
        <div class="mm-content-wrap">
            <div class="mm-content" id="mmContent">
                {{-- Folders row --}}
                <div id="mmFolderRow" class="mm-folders-row" style="display:none"></div>

                {{-- Grid --}}
                <div id="mmSectionLabel" class="mm-section-label" style="display:none">Files</div>
                <div class="mm-spinner" id="mmSpinner"><i class="mdi mdi-loading"></i> Đang tải...</div>
                <div class="mm-grid" id="mmGrid"></div>
                <div class="mm-list" id="mmList" style="display:none">
                    <div class="mm-list-head">
                        <span></span>
                        <span></span>
                        <span>Tên</span>
                        <span>Loại</span>
                        <span>Kích thước</span>
                        <span>Dung lượng</span>
                        <span>Ngày</span>
                    </div>
                    <div id="mmListBody"></div>
                </div>
                <div class="mm-empty" id="mmEmpty" style="display:none">
                    <i class="mdi mdi-image-off-outline"></i>
                    <h5>Thư mục trống</h5>
                    <p>Kéo thả file vào đây hoặc nhấn "Tải lên"</p>
                </div>
                <div id="mmLoadMore" style="text-align:center;padding:16px;display:none">
                    <button class="mm-btn" id="btnLoadMore">Tải thêm...</button>
                </div>
            </div>

            {{-- Details Panel --}}
            <div class="mm-details" id="mmDetails">
                <div class="mm-details-head">
                    <h5 id="mmDetailsTitle">—</h5>
                    <button class="mm-details-close" onclick="mmCloseDetails()"><i class="mdi mdi-close"></i></button>
                </div>
                <div class="mm-details-preview" id="mmDetailsPreview"></div>
                <div class="mm-details-body" id="mmDetailsBody"></div>
                <div class="mm-details-actions" id="mmDetailsActions"></div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk action bar --}}
<div class="mm-bulk-bar" id="mmBulkBar">
    <span class="mm-bulk-info"><span id="mmBulkCount">0</span> file đã chọn</span>
    <div class="mm-bulk-actions">
        <button class="mm-btn" onclick="mmBulkMove()"><i class="mdi mdi-folder-move"></i> Di chuyển</button>
        <button class="mm-btn mm-btn-danger" onclick="mmBulkDelete()"><i class="mdi mdi-trash-can"></i> Xóa tất cả</button>
        <button class="mm-btn" onclick="mmClearSelect()">Bỏ chọn</button>
    </div>
</div>

{{-- Upload Modal --}}
<div class="mm-modal-overlay" id="mmUploadModal">
    <div class="mm-modal">
        <div class="mm-modal-head">
            <h5>Tải lên file</h5>
            <button class="mm-modal-close" onclick="mmCloseUpload()"><i class="mdi mdi-close"></i></button>
        </div>
        <div class="mm-modal-body">
            <div class="mm-modal-field">
                <label>Thư mục đích</label>
                <select id="mmUploadFolder"></select>
            </div>
            <div class="mm-dropzone" id="mmDropzone">
                <i class="mdi mdi-cloud-upload-outline"></i>
                <p>Kéo thả file vào đây hoặc click để chọn</p>
                <span>Tối đa <span id="mmMaxFileSizeText">5MB</span>/file. Hỗ trợ nhiều file cùng lúc.</span>
            </div>
            <div class="mm-upload-queue" id="mmUploadQueue"></div>
        </div>
        <div class="mm-modal-foot">
            <div class="mm-progress-wrap" id="mmProgressWrap">
                <div class="mm-progress-track"><div class="mm-progress-bar" id="mmProgressBar"></div></div>
                <div class="mm-progress-text" id="mmProgressText">0 / 0</div>
            </div>
            <button class="mm-btn" onclick="mmCloseUpload()" id="mmBtnCloseUpload">Đóng</button>
        </div>
    </div>
</div>
<input type="file" id="mmFileInput" multiple style="display:none" accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">

{{-- Prompt Modal --}}
<div class="mm-prompt-modal" id="mmPromptModal">
    <div class="mm-prompt-box">
        <h5 id="mmPromptTitle">Tên</h5>
        <input type="text" id="mmPromptInput" placeholder="">
        <div class="mm-prompt-actions">
            <button class="mm-btn" onclick="mmPromptCancel()">Hủy</button>
            <button class="mm-btn mm-btn-primary" id="mmPromptOk" onclick="mmPromptConfirm()">OK</button>
        </div>
    </div>
</div>

{{-- Move Modal --}}
<div class="mm-prompt-modal" id="mmMoveModal">
    <div class="mm-prompt-box">
        <h5>Di chuyển đến thư mục</h5>
        <select id="mmMoveTarget" style="margin-bottom:14px;width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:5px;font-size:13.5px;outline:none;"></select>
        <div class="mm-prompt-actions">
            <button class="mm-btn" onclick="mmCloseMoveModal()">Hủy</button>
            <button class="mm-btn mm-btn-primary" onclick="mmConfirmMove()">Di chuyển</button>
        </div>
    </div>
</div>

{{-- Context Menu --}}
<div class="mm-ctx" id="mmCtx">
    <div class="mm-ctx-item" onclick="mmCtxPreview()"><i class="mdi mdi-eye-outline"></i> Xem trước</div>
    <div class="mm-ctx-item" onclick="mmCtxRename()"><i class="mdi mdi-pencil-outline"></i> Đổi tên</div>
    <div class="mm-ctx-item" onclick="mmCtxMove()"><i class="mdi mdi-folder-move-outline"></i> Di chuyển</div>
    <div class="mm-ctx-item" onclick="mmCtxCopyUrl()"><i class="mdi mdi-link-variant"></i> Sao chép URL</div>
    <div class="mm-ctx-sep"></div>
    <div class="mm-ctx-item danger" onclick="mmCtxDelete()"><i class="mdi mdi-trash-can-outline"></i> Xóa</div>
</div>

{{-- Toast --}}
<div class="mm-toast" id="mmToast"></div>

@endsection

@section('scripts')
<script>
// ════════════════════════════════════════════════════════════
//  STATE
// ════════════════════════════════════════════════════════════
const urlParams = new URLSearchParams(window.location.search);
const isPicker = urlParams.has('picker');
const isMultiple = urlParams.get('multiple') === '1';

const mm = {
    folder:    '',
    items:     [],        // current page files
    selected:  new Set(), // selected media IDs
    view:      'grid',    // 'grid' | 'list'
    sort:      'newest',
    type:      'all',
    page:      1,
    hasMore:   false,
    loading:   false,
    detail:    null,      // current detail media object
    allFolders:[],        // flat list of all folder paths
    ctxTarget: null,      // media object for context menu
    // prompt callback
    _promptCb: null,
    _moveIds:  [],        // IDs pending move
};

// ════════════════════════════════════════════════════════════
//  INIT
// ════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    const initialFolder = urlParams.get('folder') || '';
    loadTree();
    navigate(initialFolder);

    // Search debounce
    let searchTimer;
    document.getElementById('mmSearch').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => { mm.page = 1; loadFiles(); }, 400);
    });

    // Sort / type
    document.getElementById('mmSort').addEventListener('change', e => { mm.sort = e.target.value; mm.page = 1; loadFiles(); });
    document.getElementById('mmType').addEventListener('change', e => { mm.type = e.target.value; mm.page = 1; loadFiles(); });

    // View toggle
    document.getElementById('btnGrid').addEventListener('click', () => setView('grid'));
    document.getElementById('btnList').addEventListener('click', () => setView('list'));

    // Upload
    document.getElementById('btnUpload').addEventListener('click', openUploadModal);
    document.getElementById('mmDropzone').addEventListener('click', () => document.getElementById('mmFileInput').click());
    document.getElementById('mmFileInput').addEventListener('change', function() {
        if (this.files.length > 0) startUpload(this.files);
    });

    // New folder
    document.getElementById('btnNewFolder').addEventListener('click', promptNewFolder);

    // Sync
    document.getElementById('btnSync').addEventListener('click', syncMedia);

    // Load more
    document.getElementById('btnLoadMore').addEventListener('click', () => { mm.page++; loadFiles(true); });

    // Drag & drop on dropzone
    setupDropzone();

    // Close context menu on click outside
    document.addEventListener('click', e => {
        if (!document.getElementById('mmCtx').contains(e.target)) {
            document.getElementById('mmCtx').classList.remove('show');
        }
    });

    // Escape to close panels
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.getElementById('mmCtx').classList.remove('show');
            mmCloseDetails();
        }
        if (e.key === 'Delete' && mm.selected.size > 0) {
            mmBulkDelete();
        }
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            selectAll();
        }
    });
});

// ════════════════════════════════════════════════════════════
//  TREE
// ════════════════════════════════════════════════════════════
async function loadTree() {
    try {
        const data = await api('GET', '{{ route("admin.api.media.tree") }}');
        if (data.success) {
            mm.allFolders = flattenFolders(data.tree);
            renderTree(data.tree, document.getElementById('mmTree'), 0);
            highlightTreeNode(mm.folder);
        }
    } catch (e) {}
}

function flattenFolders(tree, list = []) {
    for (const node of tree) {
        list.push(node.path);
        flattenFolders(node.children, list);
    }
    return list;
}

function renderTree(nodes, container, depth) {
    container.innerHTML = '';
    // Add root item
    if (depth === 0) {
        const root = document.createElement('div');
        root.className = 'mm-tree-item' + (mm.folder === '' ? ' active' : '');
        root.style.paddingLeft = '8px';
        root.innerHTML = `<span class="mm-ti-icon"><i class="mdi mdi-server"></i></span><span class="mm-ti-name">Storage root</span>`;
        root.addEventListener('click', () => navigate(''));
        container.appendChild(root);
    }
    for (const node of nodes) {
        const hasChildren = node.children && node.children.length > 0;
        const item = document.createElement('div');
        item.className = 'mm-tree-item' + (mm.folder === node.path ? ' active' : '');
        item.style.paddingLeft = (depth * 14 + 14) + 'px';
        item.dataset.path = node.path;
        item.innerHTML = `
            <span class="mm-ti-arrow ${hasChildren ? '' : 'invisible'}">${hasChildren ? '▶' : ''}</span>
            <span class="mm-ti-icon"><i class="mdi mdi-folder"></i></span>
            <span class="mm-ti-name">${escHtml(node.name)}</span>`;
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            navigate(node.path);
        });
        container.appendChild(item);

        if (hasChildren) {
            const childWrap = document.createElement('div');
            childWrap.className = 'mm-tree-children';
            renderTree(node.children, childWrap, depth + 1);
            container.appendChild(childWrap);

            item.querySelector('.mm-ti-arrow').addEventListener('click', (e) => {
                e.stopPropagation();
                const arrow = item.querySelector('.mm-ti-arrow');
                const open  = childWrap.classList.toggle('open');
                arrow.classList.toggle('open', open);
            });
        }
    }
}

function highlightTreeNode(folder) {
    document.querySelectorAll('.mm-tree-item').forEach(el => {
        const isActive = (el.dataset.path || '') === folder;
        el.classList.toggle('active', isActive);
        
        if (isActive) {
            let parentWrapper = el.parentElement;
            while (parentWrapper && parentWrapper.classList.contains('mm-tree-children')) {
                parentWrapper.classList.add('open');
                const parentItem = parentWrapper.previousElementSibling;
                if (parentItem && parentItem.classList.contains('mm-tree-item')) {
                    const arrow = parentItem.querySelector('.mm-ti-arrow');
                    if (arrow) arrow.classList.add('open');
                }
                parentWrapper = parentWrapper.parentElement;
            }
        }
    });
}

// ════════════════════════════════════════════════════════════
//  NAVIGATE
// ════════════════════════════════════════════════════════════
function navigate(folder) {
    mm.folder  = folder;
    mm.page    = 1;
    mm.selected.clear();
    updateBulkBar();
    highlightTreeNode(folder);
    renderBreadcrumb(folder);
    mmCloseDetails();
    loadFiles();
}

function renderBreadcrumb(folder) {
    const bc  = document.getElementById('mmBreadcrumb');
    bc.innerHTML = '';
    const parts = [''].concat(folder ? folder.split('/') : []);
    const paths = [];
    parts.forEach((part, i) => {
        if (i > 0) {
            paths.push(paths[i - 1] ? paths[i - 1] + '/' + part : part);
        } else {
            paths.push('');
        }

        if (i > 0) {
            const sep = document.createElement('span');
            sep.className = 'mm-bc-sep';
            sep.textContent = '/';
            bc.appendChild(sep);
        }

        const el = document.createElement('span');
        el.className = 'mm-bc-item' + (i === parts.length - 1 ? ' active' : '');
        el.textContent = i === 0 ? 'Storage' : part;
        if (i < parts.length - 1) {
            el.addEventListener('click', () => navigate(paths[i]));
        }
        bc.appendChild(el);
    });
}

// ════════════════════════════════════════════════════════════
//  LOAD FILES
// ════════════════════════════════════════════════════════════
async function loadFiles(append = false) {
    if (mm.loading) return;
    mm.loading = true;

    if (!append) {
        mm.items = [];
        document.getElementById('mmGrid').innerHTML = '';
        document.getElementById('mmListBody').innerHTML = '';
        document.getElementById('mmFolderRow').style.display = 'none';
        document.getElementById('mmFolderRow').innerHTML = '';
    }

    document.getElementById('mmSpinner').classList.add('show');
    document.getElementById('mmEmpty').style.display = 'none';
    document.getElementById('mmLoadMore').style.display = 'none';

    const search = document.getElementById('mmSearch').value;

    try {
        const params = new URLSearchParams({
            folder:   mm.folder,
            sort:     mm.sort,
            type:     mm.type,
            page:     mm.page,
            per_page: 48,
        });
        if (search) params.set('search', search);

        const data = await api('GET', '{{ route("admin.api.media.browse") }}?' + params.toString());
        if (!data.success) throw new Error(data.message);

        mm.hasMore = data.has_more;

        // Render subfolders (only on first load, not append)
        if (!append && data.subfolders && data.subfolders.length > 0) {
            renderFolders(data.subfolders);
        }

        // Render files
        if (data.files.length > 0) {
            mm.items = mm.items.concat(data.files);
            data.files.forEach(f => renderFileItem(f));
        }

        const totalItems = mm.items.length + (data.subfolders?.length ?? 0);
        if (!append && totalItems === 0) {
            document.getElementById('mmEmpty').style.display = 'flex';
        }

        document.getElementById('mmSectionLabel').style.display = mm.items.length ? 'block' : 'none';
        document.getElementById('mmLoadMore').style.display = mm.hasMore ? 'block' : 'none';

    } catch (e) {
        toast(e.message || 'Lỗi tải dữ liệu', 'error');
    } finally {
        mm.loading = false;
        document.getElementById('mmSpinner').classList.remove('show');
    }
}

// ════════════════════════════════════════════════════════════
//  RENDER FOLDERS
// ════════════════════════════════════════════════════════════
function renderFolders(folders) {
    const row = document.getElementById('mmFolderRow');
    row.style.display = 'flex';
    folders.forEach(f => {
        const el = document.createElement('div');
        el.className = 'mm-folder-item';
        el.innerHTML = `
            <i class="mdi mdi-folder"></i>
            <span>${escHtml(f.name)}</span>
            <span class="mm-folder-count">(${f.count})</span>
            <span class="mm-folder-actions">
                <span class="mm-folder-action-btn" title="Đổi tên" onclick="promptRenameFolder('${escHtml(f.path)}','${escHtml(f.name)}',event)"><i class="mdi mdi-pencil-outline"></i></span>
                <span class="mm-folder-action-btn danger" title="Xóa thư mục" onclick="deleteFolder('${escHtml(f.path)}',event)"><i class="mdi mdi-trash-can-outline"></i></span>
            </span>`;
        el.addEventListener('dblclick', () => navigate(f.path));
        el.addEventListener('click', (e) => { if (!e.target.closest('.mm-folder-actions')) navigate(f.path); });
        row.appendChild(el);
    });
}

// ════════════════════════════════════════════════════════════
//  RENDER FILE ITEMS
// ════════════════════════════════════════════════════════════
function renderFileItem(f) {
    if (mm.view === 'grid') renderFileGrid(f);
    else renderFileList(f);
}

function renderFileGrid(f) {
    const grid = document.getElementById('mmGrid');
    const el   = document.createElement('div');
    el.className = 'mm-file-item' + (f.status === 'missing' ? ' missing' : '');
    el.dataset.id = f.id;

    const thumb = f.is_image
        ? `<img src="${escHtml(f.url)}" loading="lazy" alt="${escHtml(f.alt || f.filename)}">`
        : `<i class="${mmIconClass(f.mime_type, f.extension)}" style="color:#94a3b8"></i>`;

    el.innerHTML = `
        <div class="mm-file-chk"><i class="mdi mdi-check"></i></div>
        <div class="mm-file-thumb">${thumb}</div>
        <div class="mm-file-info">
            <div class="mm-file-name" title="${escHtml(f.filename)}">${escHtml(f.title || f.filename)}</div>
            <div class="mm-file-meta">${f.size_human}</div>
        </div>
        ${f.extension ? `<div class="mm-file-badge">${escHtml(f.extension)}</div>` : ''}`;

    el.addEventListener('click',       (e) => handleFileClick(e, f));
    el.addEventListener('contextmenu', (e) => { e.preventDefault(); openCtxMenu(e, f); });
    grid.appendChild(el);
}

function renderFileList(f) {
    const body = document.getElementById('mmListBody');
    const row  = document.createElement('div');
    row.className = 'mm-list-row' + (f.status === 'missing' ? ' missing' : '');
    row.dataset.id = f.id;

    const thumb = f.is_image
        ? `<img class="mm-list-thumb" src="${escHtml(f.url)}" loading="lazy" alt="">`
        : `<i class="mm-list-icon ${mmIconClass(f.mime_type, f.extension)}"></i>`;

    const dims = f.width ? `${f.width}×${f.height}` : '—';

    row.innerHTML = `
        <input type="checkbox" class="mm-list-chk" onchange="toggleSelect(${f.id}, this.checked)">
        <div>${thumb}</div>
        <div class="mm-list-name" title="${escHtml(f.filename)}">${escHtml(f.title || f.filename)}</div>
        <div style="font-size:11.5px;color:#64748b">${escHtml(f.extension?.toUpperCase() || '—')}</div>
        <div style="font-size:12px;color:#94a3b8">${dims}</div>
        <div style="font-size:12.5px">${f.size_human}</div>
        <div style="font-size:12px;color:#94a3b8">${f.created_at}</div>`;

    row.addEventListener('click',       (e) => { if (e.target.type !== 'checkbox') handleFileClick(e, f); });
    row.addEventListener('contextmenu', (e) => { e.preventDefault(); openCtxMenu(e, f); });
    body.appendChild(row);
}

function handleFileClick(e, f) {
    if (e.ctrlKey || e.metaKey) {
        toggleSelect(f.id);
    } else if (e.shiftKey) {
        // Range select
        const ids = mm.items.map(i => i.id);
        const from = ids.findIndex(id => mm.selected.size ? mm.selected.values().next().value === id : false);
        const to   = ids.indexOf(f.id);
        if (from >= 0 && to >= 0) {
            const [a, b] = [Math.min(from, to), Math.max(from, to)];
            ids.slice(a, b + 1).forEach(id => toggleSelect(id, true));
        } else {
            toggleSelect(f.id);
        }
    } else {
        openDetails(f);
    }
}

// ════════════════════════════════════════════════════════════
//  SELECT
// ════════════════════════════════════════════════════════════
function toggleSelect(id, force) {
    id = parseInt(id);
    if (force === true) {
        mm.selected.add(id);
    } else if (force === false) {
        mm.selected.delete(id);
    } else {
        mm.selected.has(id) ? mm.selected.delete(id) : mm.selected.add(id);
    }
    updateFileSelectedState(id);
    updateBulkBar();
}

function updateFileSelectedState(id) {
    const el = document.querySelector(`.mm-file-item[data-id="${id}"], .mm-list-row[data-id="${id}"]`);
    if (el) el.classList.toggle('selected', mm.selected.has(id));
    const cb = el?.querySelector('.mm-list-chk');
    if (cb) cb.checked = mm.selected.has(id);
}

function selectAll() {
    mm.items.forEach(f => { mm.selected.add(f.id); updateFileSelectedState(f.id); });
    updateBulkBar();
}

function mmClearSelect() {
    mm.items.forEach(f => { mm.selected.delete(f.id); updateFileSelectedState(f.id); });
    updateBulkBar();
}

function updateBulkBar() {
    const bar = document.getElementById('mmBulkBar');
    document.getElementById('mmBulkCount').textContent = mm.selected.size;
    
    if (isPicker) {
        document.querySelector('.mm-bulk-actions').innerHTML = `
            <button class="mm-btn mm-btn-primary" onclick="mmPickerInsert()" style="font-weight: bold;">
                <i class="mdi mdi-check"></i> Chọn ${mm.selected.size} file
            </button>
            <button class="mm-btn" onclick="mmClearSelect()">Bỏ chọn</button>
        `;
    }
    
    bar.classList.toggle('show', mm.selected.size > 0);
}

function mmPickerInsert() {
    const selectedMedia = mm.items.filter(f => mm.selected.has(f.id));
    if (selectedMedia.length === 0) return;
    window.parent.postMessage({ type: 'media_selected', data: selectedMedia }, '*');
}

// ════════════════════════════════════════════════════════════
//  VIEW
// ════════════════════════════════════════════════════════════
function setView(view) {
    mm.view = view;
    document.getElementById('btnGrid').classList.toggle('active', view === 'grid');
    document.getElementById('btnList').classList.toggle('active', view === 'list');
    document.getElementById('mmGrid').style.display = view === 'grid' ? 'grid' : 'none';
    document.getElementById('mmList').style.display = view === 'list' ? 'block' : 'none';

    // Re-render current items in new view
    document.getElementById('mmGrid').innerHTML = '';
    document.getElementById('mmListBody').innerHTML = '';
    mm.items.forEach(f => renderFileItem(f));
}

// ════════════════════════════════════════════════════════════
//  DETAILS PANEL
// ════════════════════════════════════════════════════════════
function openDetails(f) {
    mm.detail = f;
    document.getElementById('mmDetailsTitle').textContent = f.filename;

    // Preview
    const preview = document.getElementById('mmDetailsPreview');
    preview.innerHTML = f.is_image
        ? `<img src="${escHtml(f.url)}" alt="${escHtml(f.alt || '')}">`
        : `<i class="${mmIconClass(f.mime_type, f.extension)}" style="font-size:64px;color:#94a3b8"></i>`;

    // Body
    const body = document.getElementById('mmDetailsBody');
    body.innerHTML = `
        <div class="mm-field">
            <label>Tiêu đề (Title)</label>
            <input type="text" id="detTitle" value="${escAttr(f.title)}" placeholder="Tiêu đề ảnh...">
        </div>
        <div class="mm-field">
            <label>Mô tả thay thế (Alt)</label>
            <input type="text" id="detAlt" value="${escAttr(f.alt)}" placeholder="Alt text SEO...">
        </div>
        <div class="mm-field">
            <label>Ghi chú (Notes)</label>
            <textarea id="detNotes" rows="2" placeholder="Ghi chú...">${escHtml(f.notes)}</textarea>
        </div>
        <div class="mm-field">
            <label>URL Công khai</label>
            <div class="mm-field-copy">
                <input type="text" id="detUrl" value="${escAttr(f.url)}" readonly>
                <button class="mm-copy-btn" onclick="copyUrl()"><i class="mdi mdi-content-copy"></i></button>
            </div>
        </div>
        <div style="margin-top:12px;">
            <div class="mm-meta-row"><span class="mm-meta-label">Tên file</span><span class="mm-meta-value">${escHtml(f.filename)}</span></div>
            <div class="mm-meta-row"><span class="mm-meta-label">Thư mục</span><span class="mm-meta-value">${escHtml(f.folder || 'root')}</span></div>
            <div class="mm-meta-row"><span class="mm-meta-label">Dung lượng</span><span class="mm-meta-value">${f.size_human}</span></div>
            ${f.width ? `<div class="mm-meta-row"><span class="mm-meta-label">Kích thước</span><span class="mm-meta-value">${f.width}×${f.height}px</span></div>` : ''}
            <div class="mm-meta-row"><span class="mm-meta-label">MIME</span><span class="mm-meta-value">${escHtml(f.mime_type || '—')}</span></div>
            <div class="mm-meta-row"><span class="mm-meta-label">Ngày tải lên</span><span class="mm-meta-value">${f.created_at}</span></div>
        </div>`;

    // Actions
    let actionsHtml = '';
    if (isPicker) {
        actionsHtml = `
            <button class="mm-btn mm-btn-primary" onclick="mmPickerInsertSingle()" style="flex:1; font-weight: bold; background: #10b981; border-color: #10b981; color: #fff;">
                <i class="mdi mdi-check"></i> Chọn file này
            </button>`;
    } else {
        actionsHtml = `
            <button class="mm-btn" onclick="saveMeta()" style="flex:1">
                <i class="mdi mdi-content-save-outline"></i> Lưu
            </button>
            <button class="mm-btn" onclick="promptRenameFile()"><i class="mdi mdi-pencil-outline"></i> Đổi tên</button>
            <button class="mm-btn" onclick="openMoveModal([${f.id}])"><i class="mdi mdi-folder-move-outline"></i> Di chuyển</button>
            <button class="mm-btn mm-btn-danger" onclick="deleteFiles([${f.id}])"><i class="mdi mdi-trash-can-outline"></i> Xóa</button>`;
    }
    document.getElementById('mmDetailsActions').innerHTML = actionsHtml;

    document.getElementById('mmDetails').classList.add('open');
}

function mmPickerInsertSingle() {
    if (!mm.detail) return;
    window.parent.postMessage({ type: 'media_selected', data: [mm.detail] }, '*');
}

function mmCloseDetails() {
    document.getElementById('mmDetails').classList.remove('open');
    mm.detail = null;
}

async function saveMeta() {
    if (!mm.detail) return;
    try {
        const data = await api('PUT', `/admin/api/media/${mm.detail.id}`, {
            title: document.getElementById('detTitle').value,
            alt:   document.getElementById('detAlt').value,
            notes: document.getElementById('detNotes').value,
        });
        if (!data.success) throw new Error(data.message);
        // Update local state
        const idx = mm.items.findIndex(f => f.id === mm.detail.id);
        if (idx >= 0) { mm.items[idx] = data.item; mm.detail = data.item; }
        toast('Đã lưu metadata!', 'success');
    } catch (e) { toast(e.message, 'error'); }
}

function copyUrl() {
    const el = document.getElementById('detUrl');
    el.select();
    document.execCommand('copy');
    toast('Đã sao chép URL!', 'success');
}

// ════════════════════════════════════════════════════════════
//  RENAME FILE (prompt)
// ════════════════════════════════════════════════════════════
function promptRenameFile(f = null) {
    const target = f || mm.detail;
    if (!target) return;
    openPrompt('Đổi tên file', target.filename, async (newName) => {
        if (!newName || newName === target.filename) return;
        try {
            const data = await api('POST', `/admin/api/media/${target.id}/rename`, { filename: newName });
            if (!data.success) throw new Error(data.message);
            toast('Đã đổi tên thành công!', 'success');
            refreshCurrentFile(data.item);
        } catch (e) { toast(e.message, 'error'); }
    });
}

function refreshCurrentFile(updated) {
    const idx = mm.items.findIndex(f => f.id === updated.id);
    if (idx >= 0) {
        mm.items[idx] = updated;
        // Re-render that item
        const el = document.querySelector(`.mm-file-item[data-id="${updated.id}"], .mm-list-row[data-id="${updated.id}"]`);
        if (el) {
            const tmp = document.createElement('div');
            tmp.innerHTML = ''; // placeholder
            if (mm.view === 'grid') {
                renderFileGrid(updated);
                el.replaceWith(document.getElementById('mmGrid').lastElementChild);
            } else {
                renderFileList(updated);
                el.replaceWith(document.getElementById('mmListBody').lastElementChild);
            }
        }
        if (mm.detail?.id === updated.id) openDetails(updated);
    }
}

// ════════════════════════════════════════════════════════════
//  DELETE
// ════════════════════════════════════════════════════════════
async function deleteFiles(ids) {
    if (!ids || ids.length === 0) return;
    const label = ids.length === 1 ? '1 file này' : `${ids.length} file đã chọn`;
    if (!confirm(`Xóa vĩnh viễn ${label}? Thao tác này không thể hoàn tác.`)) return;

    try {
        const data = await api('POST', '{{ route("admin.api.media.delete") }}', { ids });
        if (!data.success && !data.result) throw new Error(data.message);
        toast(`Đã xóa ${data.result?.deleted ?? ids.length} file!`, 'success');
        // Remove from DOM and state
        ids.forEach(id => {
            mm.items = mm.items.filter(f => f.id !== id);
            mm.selected.delete(id);
            document.querySelector(`.mm-file-item[data-id="${id}"], .mm-list-row[data-id="${id}"]`)?.remove();
        });
        mmCloseDetails();
        updateBulkBar();
        if (mm.items.length === 0) document.getElementById('mmEmpty').style.display = 'flex';
    } catch (e) { toast(e.message, 'error'); }
}

async function mmBulkDelete() {
    if (!mm.selected.size) return;
    await deleteFiles(Array.from(mm.selected));
}

// ════════════════════════════════════════════════════════════
//  MOVE
// ════════════════════════════════════════════════════════════
function openMoveModal(ids) {
    mm._moveIds = ids;
    const sel = document.getElementById('mmMoveTarget');
    sel.innerHTML = mm.allFolders
        .filter(p => p !== mm.folder)
        .map(p => `<option value="${escAttr(p)}">${escHtml(p || 'Storage root')}</option>`)
        .join('');
    document.getElementById('mmMoveModal').classList.add('show');
}

function mmCloseMoveModal() {
    document.getElementById('mmMoveModal').classList.remove('show');
    mm._moveIds = [];
}

async function mmConfirmMove() {
    const destFolder = document.getElementById('mmMoveTarget').value;
    mmCloseMoveModal();

    for (const id of mm._moveIds) {
        try {
            const data = await api('POST', `/admin/api/media/${id}/move`, { folder: destFolder });
            if (!data.success) { toast(`Lỗi: ${data.message}`, 'error'); continue; }
            mm.items = mm.items.filter(f => f.id !== id);
            mm.selected.delete(id);
            document.querySelector(`.mm-file-item[data-id="${id}"], .mm-list-row[data-id="${id}"]`)?.remove();
        } catch (e) { toast(e.message, 'error'); }
    }

    toast('Di chuyển xong!', 'success');
    mmCloseDetails();
    updateBulkBar();
    if (mm.items.length === 0) document.getElementById('mmEmpty').style.display = 'flex';
}

function mmBulkMove() {
    if (!mm.selected.size) return;
    openMoveModal(Array.from(mm.selected));
}

// ════════════════════════════════════════════════════════════
//  FOLDER MANAGEMENT
// ════════════════════════════════════════════════════════════
function promptNewFolder() {
    openPrompt('Tên thư mục mới', '', async (name) => {
        if (!name.trim()) return;
        const path = mm.folder ? mm.folder + '/' + name.trim() : name.trim();
        try {
            const data = await api('POST', '{{ route("admin.api.media.folder.create") }}', { path });
            if (!data.success) throw new Error(data.message);
            toast('Tạo thư mục thành công!', 'success');
            await loadTree();
            navigate(path);
        } catch (e) { toast(e.message, 'error'); }
    });
}

function promptRenameFolder(oldPath, oldName, e) {
    e?.stopPropagation();
    openPrompt(`Đổi tên thư mục "${oldName}"`, oldName, async (newName) => {
        if (!newName.trim() || newName.trim() === oldName) return;
        const parentPath = oldPath.includes('/')
            ? oldPath.substring(0, oldPath.lastIndexOf('/'))
            : '';
        const newPath = parentPath ? parentPath + '/' + newName.trim() : newName.trim();
        try {
            const data = await api('PUT', '{{ route("admin.api.media.folder.rename") }}', { old_path: oldPath, new_path: newPath });
            if (!data.success) throw new Error(data.message);
            toast('Đổi tên thư mục thành công!', 'success');
            await loadTree();
            if (mm.folder === oldPath) navigate(newPath);
            else { mm.page = 1; loadFiles(); }
        } catch (e) { toast(e.message, 'error'); }
    });
}

async function deleteFolder(path, e) {
    e?.stopPropagation();
    if (!confirm(`Xóa thư mục "${path}"? Tất cả file bên trong sẽ bị xóa vĩnh viễn!`)) return;
    try {
        const data = await api('DELETE', '{{ route("admin.api.media.folder.delete") }}', { path, force: true });
        if (!data.success) throw new Error(data.message);
        toast('Xóa thư mục thành công!', 'success');
        await loadTree();
        if (mm.folder === path || mm.folder.startsWith(path + '/')) navigate('');
        else { mm.page = 1; loadFiles(); }
    } catch (e) { toast(e.message, 'error'); }
}

// ════════════════════════════════════════════════════════════
//  UPLOAD
// ════════════════════════════════════════════════════════════
function openUploadModal() {
    // Populate folder dropdown
    const folderNames = {
        'admins/images/card': 'Admin - Ảnh Card',
        'admins/images/avatar': 'Admin - Ảnh Đại Diện',
        'admins/images/product': 'Admin - Ảnh Sản Phẩm',
        'admins/images/profile': 'Admin - Ảnh Profile',
        'admins/images/svg': 'Admin - File SVG',
        'admins/images/tab': 'Admin - Ảnh Tab',
        'clients/imgs/banners': 'Website - Banners',
        'clients/imgs/business': 'Website - Doanh Nghiệp',
        'clients/imgs/catalogs': 'Website - Tài Liệu Catalog',
        'clients/imgs/categories': 'Website - Danh Mục',
        'clients/imgs/icons': 'Website - Icons',
        'clients/imgs/others': 'Website - Khác',
        'clients/imgs/posts': 'Website - Bài Viết',
        'clients/imgs/products': 'Website - Sản Phẩm',
        'clients/imgs/users': 'Website - Người Dùng'
    };
    const excludeFolders = ['admins', 'admins/images', 'clients', 'clients/imgs', ''];

    const sel = document.getElementById('mmUploadFolder');
    sel.innerHTML = mm.allFolders
        .filter(p => !excludeFolders.includes(p))
        .map(p => {
            const name = folderNames[p] || p;
            return `<option value="${escAttr(p)}" ${p === mm.folder ? 'selected' : ''}>${escHtml(name)}</option>`;
        })
        .join('');
        
    function updateLimitText() {
        const val = sel.value;
        const textSpan = document.getElementById('mmMaxFileSizeText');
        if (textSpan) {
            textSpan.textContent = (val && val.includes('catalogs')) ? '100MB' : '5MB';
        }
    }
    sel.onchange = updateLimitText;
    updateLimitText();

    document.getElementById('mmUploadQueue').innerHTML = '';
    document.getElementById('mmUploadQueue').style.display = 'none';
    document.getElementById('mmProgressWrap').classList.remove('show');
    document.getElementById('mmProgressBar').style.width = '0%';
    document.getElementById('mmProgressText').textContent = '0 / 0';
    document.getElementById('mmBtnCloseUpload').disabled = false;
    document.getElementById('mmUploadModal').classList.add('show');
}

function mmCloseUpload() {
    if (document.getElementById('mmBtnCloseUpload').disabled) return;
    document.getElementById('mmUploadModal').classList.remove('show');
}

function setupDropzone() {
    const dz = document.getElementById('mmDropzone');
    let cnt = 0;
    dz.addEventListener('dragenter', e => { e.preventDefault(); if (++cnt === 1) dz.classList.add('drag'); });
    dz.addEventListener('dragleave', () => { if (--cnt === 0) dz.classList.remove('drag'); });
    dz.addEventListener('dragover',  e => e.preventDefault());
    dz.addEventListener('drop', e => {
        e.preventDefault(); cnt = 0; dz.classList.remove('drag');
        if (e.dataTransfer.files.length > 0) startUpload(e.dataTransfer.files);
    });
}

async function startUpload(files) {
    const targetFolder = document.getElementById('mmUploadFolder')?.value ?? mm.folder;
    const total = files.length;
    if (total === 0) return;

    document.getElementById('mmBtnCloseUpload').disabled = true;
    const qEl    = document.getElementById('mmUploadQueue');
    const pBar   = document.getElementById('mmProgressBar');
    const pText  = document.getElementById('mmProgressText');
    const pWrap  = document.getElementById('mmProgressWrap');

    qEl.style.display = 'block';
    pWrap.classList.add('show');
    pBar.style.width = '0%';
    pText.textContent = `0 / ${total}`;

    const CHUNK = 5, THREADS = 3;
    const arr    = Array.from(files);
    const chunks = [];
    for (let i = 0; i < arr.length; i += CHUNK) chunks.push(arr.slice(i, i + CHUNK));

    let chunkIdx = 0, donePct = 0;

    const runThread = async () => {
        while (chunkIdx < chunks.length) {
            const chunk = chunks[chunkIdx++];
            const fd    = new FormData();
            fd.append('target_folder', targetFolder);
            chunk.forEach(f => fd.append('files[]', f));

            const row = document.createElement('div');
            row.className = 'mm-queue-item';
            row.innerHTML = `<span class="q-name">${escHtml(chunk.map(f=>f.name).join(', '))}</span><span class="q-status">Đang tải...</span>`;
            qEl.appendChild(row);
            qEl.scrollTop = qEl.scrollHeight;

            try {
                const res  = await fetch('{{ route("admin.api.media.upload") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: fd,
                });
                const data = await res.json();
                if (data.success) {
                    row.classList.add('ok');
                    row.querySelector('.q-status').textContent = `✓ ${data.uploaded?.length ?? chunk.length} file`;
                } else {
                    row.classList.add('err');
                    row.querySelector('.q-status').textContent = data.message || 'Lỗi';
                }
            } catch (e) {
                row.classList.add('err');
                row.querySelector('.q-status').textContent = 'Mất mạng';
            }

            donePct += chunk.length;
            pBar.style.width = Math.round((donePct / total) * 100) + '%';
            pText.textContent = `${Math.min(donePct, total)} / ${total}`;
        }
    };

    await Promise.all(Array.from({ length: Math.min(THREADS, chunks.length) }, runThread));

    document.getElementById('mmBtnCloseUpload').disabled = false;
    document.getElementById('mmFileInput').value = '';
    toast(`Tải lên hoàn tất!`, 'success');

    // Reload if still in same folder
    if (document.getElementById('mmUploadFolder')?.value === mm.folder || !mm.folder) {
        mm.page = 1;
        loadFiles();
    }
}

// ════════════════════════════════════════════════════════════
//  SYNC
// ════════════════════════════════════════════════════════════
async function syncMedia() {
    const btn = document.getElementById('btnSync');
    btn.disabled = true;
    toast('Đang đồng bộ filesystem...', '');
    try {
        const data = await api('POST', '{{ route("admin.api.media.sync") }}', { folder: mm.folder || null });
        if (!data.success) throw new Error(data.message);
        const r = data.report;
        toast(`Sync xong! Import: ${r.imported}, Missing: ${r.marked_missing}`, 'success');
        await loadTree();
        mm.page = 1; loadFiles();
    } catch (e) { toast(e.message, 'error'); }
    finally { btn.disabled = false; }
}

// ════════════════════════════════════════════════════════════
//  CONTEXT MENU
// ════════════════════════════════════════════════════════════
function openCtxMenu(e, f) {
    mm.ctxTarget = f;
    const ctx = document.getElementById('mmCtx');
    ctx.style.left = `${Math.min(e.clientX, window.innerWidth - 170)}px`;
    ctx.style.top  = `${Math.min(e.clientY, window.innerHeight - 200)}px`;
    ctx.classList.add('show');
}
function mmCtxPreview() { document.getElementById('mmCtx').classList.remove('show'); if (mm.ctxTarget) openDetails(mm.ctxTarget); }
function mmCtxRename()  { document.getElementById('mmCtx').classList.remove('show'); promptRenameFile(mm.ctxTarget); }
function mmCtxMove()    { document.getElementById('mmCtx').classList.remove('show'); if (mm.ctxTarget) openMoveModal([mm.ctxTarget.id]); }
function mmCtxDelete()  { document.getElementById('mmCtx').classList.remove('show'); if (mm.ctxTarget) deleteFiles([mm.ctxTarget.id]); }
function mmCtxCopyUrl() {
    document.getElementById('mmCtx').classList.remove('show');
    if (mm.ctxTarget) { navigator.clipboard.writeText(mm.ctxTarget.url).then(() => toast('Đã sao chép URL!', 'success')); }
}

// ════════════════════════════════════════════════════════════
//  PROMPT HELPER
// ════════════════════════════════════════════════════════════
function openPrompt(title, value, callback) {
    document.getElementById('mmPromptTitle').textContent = title;
    document.getElementById('mmPromptInput').value = value;
    document.getElementById('mmPromptModal').classList.add('show');
    setTimeout(() => {
        const input = document.getElementById('mmPromptInput');
        input.focus(); input.select();
    }, 50);
    mm._promptCb = callback;
}
function mmPromptConfirm() {
    const val = document.getElementById('mmPromptInput').value.trim();
    document.getElementById('mmPromptModal').classList.remove('show');
    if (mm._promptCb) { mm._promptCb(val); mm._promptCb = null; }
}
function mmPromptCancel() {
    document.getElementById('mmPromptModal').classList.remove('show');
    mm._promptCb = null;
}
document.addEventListener('keydown', e => {
    if (e.key === 'Enter' && document.getElementById('mmPromptModal').classList.contains('show')) {
        mmPromptConfirm();
    }
});

// ════════════════════════════════════════════════════════════
//  TOAST
// ════════════════════════════════════════════════════════════
let toastTimer;
function toast(msg, type = '') {
    const el = document.getElementById('mmToast');
    el.className = 'mm-toast show' + (type ? ' ' + type : '');
    el.innerHTML = (type === 'success' ? '<i class="mdi mdi-check-circle"></i>' :
                    type === 'error'   ? '<i class="mdi mdi-alert-circle"></i>' : '') + ' ' + escHtml(msg);
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.remove('show'), 3500);
}

// ════════════════════════════════════════════════════════════
//  API HELPER
// ════════════════════════════════════════════════════════════
async function api(method, url, body = null) {
    const opts = {
        method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    };
    if (body !== null && method !== 'GET') {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }
    const res  = await fetch(url, opts);
    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || `HTTP ${res.status}`);
    }
    return res.json();
}

// ════════════════════════════════════════════════════════════
//  UTILITY
// ════════════════════════════════════════════════════════════
function mmIconClass(mime, ext) {
    if (!mime) {
        ext = (ext || '').toLowerCase();
        const map = {pdf:'mdi-file-pdf-box', doc:'mdi-file-word-box', docx:'mdi-file-word-box',
                     xls:'mdi-file-excel-box', xlsx:'mdi-file-excel-box', txt:'mdi-file-document-outline',
                     zip:'mdi-folder-zip-outline', mp4:'mdi-file-video-outline', mp3:'mdi-music-note'};
        return 'mdi ' + (map[ext] || 'mdi-file-outline');
    }
    if (mime.startsWith('image/'))     return 'mdi mdi-image-outline';
    if (mime.startsWith('video/'))     return 'mdi mdi-file-video-outline';
    if (mime.startsWith('audio/'))     return 'mdi mdi-music-note';
    if (mime === 'application/pdf')    return 'mdi mdi-file-pdf-box';
    if (mime.includes('word'))         return 'mdi mdi-file-word-box';
    if (mime.includes('excel') || mime.includes('spreadsheet')) return 'mdi mdi-file-excel-box';
    return 'mdi mdi-file-outline';
}

function escHtml(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(s) { return escHtml(s); }
</script>
@endsection
