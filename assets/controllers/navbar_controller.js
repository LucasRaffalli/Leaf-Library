import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu', 'icon'];

    connect() {
        // Optional: Close menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!this.element.contains(event.target) && !this.menuTarget.classList.contains('hidden')) {
                this.toggle();
            }
        });
    }

    toggle() {
        this.menuTarget.classList.toggle('hidden');
        this.updateIcon();
    }

    updateIcon() {
        const svgPath = this.iconTarget;
        if (this.menuTarget.classList.contains('hidden')) {
            // Hamburger icon
            svgPath.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
        } else {
            // Close icon
            svgPath.setAttribute('d', 'M6 18L18 6M6 6l12 12');
        }
    }
}
