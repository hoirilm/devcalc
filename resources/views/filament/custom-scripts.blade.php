<script>
    (function () {
        const scrollToRepeaterItem = (item) => {
            if (!item) return;

            const elementRect = item.getBoundingClientRect();

            // 1. Scroll window / document root
            const windowTargetY = window.scrollY + elementRect.top - (window.innerHeight / 2);
            window.scrollTo({ top: Math.max(0, windowTargetY), behavior: 'smooth' });
            document.documentElement.scrollTo({ top: Math.max(0, windowTargetY), behavior: 'smooth' });
            document.body.scrollTo({ top: Math.max(0, windowTargetY), behavior: 'smooth' });

            // 2. Scroll any inner scrollable Filament containers
            const scrollContainers = document.querySelectorAll('main.fi-main, .fi-main, .fi-body, .fi-main-content, .fi-layout');
            scrollContainers.forEach((container) => {
                if (container.scrollHeight > container.clientHeight) {
                    const cRect = container.getBoundingClientRect();
                    const targetScrollTop = container.scrollTop + (elementRect.top - cRect.top) - (cRect.height / 2);
                    container.scrollTo({ top: Math.max(0, targetScrollTop), behavior: 'smooth' });
                }
            });

            // 3. Fallback native scrollIntoView
            try {
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (err) {}

            // 4. Highlight animation with glowing border
            item.style.transition = 'border-color 0.4s ease, box-shadow 0.4s ease';
            item.style.borderColor = 'rgba(99, 102, 241, 0.9)';
            item.style.boxShadow = '0 0 0 4px rgba(99, 102, 241, 0.4)';

            setTimeout(() => {
                item.style.borderColor = '';
                item.style.boxShadow = '';
            }, 2200);

            // 5. Auto-focus first input field in newly created row
            const input = item.querySelector('select, input:not([type="hidden"])');
            if (input) {
                setTimeout(() => input.focus({ preventScroll: true }), 250);
            }
        };

        const checkAndScrollNewItem = (previousCount) => {
            const currentItems = document.querySelectorAll('.fi-fo-repeater-item');
            if (currentItems.length > previousCount) {
                const newItem = currentItems[currentItems.length - 1];
                scrollToRepeaterItem(newItem);
                return true;
            }
            return false;
        };

        // Capture clicks on repeater add buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('button, a, [role="button"], span');
            if (!btn) return;

            const text = (btn.textContent || btn.innerText || '').toLowerCase();
            const isAddButton = text.includes('tambahkan fitur') || 
                                text.includes('tambahkan ke daftar') || 
                                text.includes('tambah item') || 
                                text.includes('add feature') ||
                                text.includes('add to') ||
                                btn.closest('.fi-fo-repeater-add-btn-container') ||
                                btn.closest('.fi-fo-repeater');

            if (isAddButton) {
                const initialCount = document.querySelectorAll('.fi-fo-repeater-item').length;
                let attempts = 0;

                const checkInterval = setInterval(() => {
                    attempts++;
                    const found = checkAndScrollNewItem(initialCount);
                    if (found || attempts > 30) {
                        clearInterval(checkInterval);
                    }
                }, 50);
            }
        }, true);

        // MutationObserver fallback to catch any dynamically added repeater item DOM nodes
        const observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                for (const node of mutation.addedNodes) {
                    if (node.nodeType === 1) {
                        let newItem = null;
                        if (node.classList && node.classList.contains('fi-fo-repeater-item')) {
                            newItem = node;
                        } else if (node.querySelector) {
                            newItem = node.querySelector('.fi-fo-repeater-item');
                        }
                        if (newItem) {
                            setTimeout(() => scrollToRepeaterItem(newItem), 100);
                        }
                    }
                }
            }
        });

        if (document.body) {
            observer.observe(document.body, { childList: true, subtree: true });
        }
    })();
</script>
