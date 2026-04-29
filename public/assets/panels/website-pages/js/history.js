// ── History Management (Undo / Redo) ──────────────────────────────────────────

let undoStack = [];
let redoStack = [];
const MAX_HISTORY = 50;
let isRestoring = false;

function saveHistory() {
  if (isRestoring) return;
  
  const container = document.getElementById('blocks-container');
  if (!container) return;

  const currentState = container.innerHTML;

  // Don't save if state is identical to last
  if (undoStack.length > 0 && undoStack[undoStack.length - 1] === currentState) {
    return;
  }

  undoStack.push(currentState);
  if (undoStack.length > MAX_HISTORY) {
    undoStack.shift();
  }

  // Clear redo stack on new action
  redoStack = [];
  updateHistoryButtons();
}

function undo() {
  if (undoStack.length <= 1) return;

  isRestoring = true;
  const container = document.getElementById('blocks-container');
  const currentState = container.innerHTML;
  
  redoStack.push(currentState);
  undoStack.pop(); // Remove current
  
  const prevState = undoStack[undoStack.length - 1];
  restoreState(prevState);
  
  setTimeout(() => { isRestoring = false; }, 100);
  updateHistoryButtons();
}

function redo() {
  if (redoStack.length === 0) return;

  isRestoring = true;
  const container = document.getElementById('blocks-container');
  const currentState = container.innerHTML;
  
  undoStack.push(currentState);
  const nextState = redoStack.pop();
  
  restoreState(nextState);
  
  setTimeout(() => { isRestoring = false; }, 100);
  updateHistoryButtons();
}

function restoreState(html) {
  const container = document.getElementById('blocks-container');
  container.innerHTML = html;

  // Re-attach listeners to all blocks
  const blocks = container.querySelectorAll('.dropped-block');
  blocks.forEach(block => {
    // Basic listeners (Badge, copy, reorder, etc.)
    attachBlockListeners(block);
    
    // Nested drop zones (Container, 2Col, 3Col, Card, Accordion)
    const dropZones = block.querySelectorAll('.col-drop-zone, .editor-container, .acc-content');
    dropZones.forEach(zone => {
      if (typeof attachDropZoneListeners === 'function') {
        attachDropZoneListeners(zone);
      }
    });

    // Re-init specific block logic
    const acc = block.querySelector('.editor-accordion');
    if (acc && typeof setupAccordionListeners === 'function') {
      setupAccordionListeners(acc);
    }
  });

  if (typeof checkEmptyBlocks === 'function') checkEmptyBlocks();
  if (typeof closeAllPanels === 'function') closeAllPanels();
}

function updateHistoryButtons() {
  const btnUndo = document.getElementById('btn-undo');
  const btnRedo = document.getElementById('btn-redo');

  if (btnUndo) {
    const canUndo = undoStack.length > 1;
    btnUndo.disabled = !canUndo;
    btnUndo.style.opacity = canUndo ? '1' : '0.5';
    btnUndo.style.pointerEvents = canUndo ? 'auto' : 'none';
  }
  if (btnRedo) {
    const canRedo = redoStack.length > 0;
    btnRedo.disabled = !canRedo;
    btnRedo.style.opacity = canRedo ? '1' : '0.5';
    btnRedo.style.pointerEvents = canRedo ? 'auto' : 'none';
    
    const icon = btnRedo.querySelector('i');
    if (icon) icon.classList.toggle('text-primary', canRedo);
  }
}

// Observe changes
function initHistoryObserver() {
  const container = document.getElementById('blocks-container');
  if (!container) return;

  let timer;
  const observer = new MutationObserver(() => {
    if (isRestoring) return;
    clearTimeout(timer);
    timer = setTimeout(() => {
      saveHistory();
    }, 500); 
  });

  observer.observe(container, {
    childList: true,
    subtree: true,
    attributes: true,
    characterData: true
  });
}

// Initialization
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    saveHistory(); 
    initHistoryObserver();
    
    document.getElementById('btn-undo')?.addEventListener('click', e => {
        e.preventDefault();
        undo();
    });
    document.getElementById('btn-redo')?.addEventListener('click', e => {
        e.preventDefault();
        redo();
    });
    
    document.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
        e.preventDefault();
        undo();
      }
      if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.shiftKey && e.key.toLowerCase() === 'z'))) {
        e.preventDefault();
        redo();
      }
    });
  }, 1000);
});
