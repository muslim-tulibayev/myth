# Blade + Alpine.js over Livewire

UI is built with standard Laravel Blade templates and Alpine.js for client-side interactions (modals, toggles, drag-and-drop sort). Livewire was the obvious alternative for a reactive dashboard but was deliberately skipped.

MYTH is a personal single-user tool where simplicity of the codebase matters more than minimising JavaScript. Blade + Alpine keeps the mental model flat — HTTP requests for mutations, Alpine for UI state only — with no Livewire component lifecycle, wire:model bindings, or server-round-trips for every interaction to reason about.

## Considered Options

- **Livewire** — reactive PHP components with minimal JS. Ideal for multi-user apps where real-time feels matter. Overhead is justified at scale but adds conceptual weight for a solo MVP.
- **Inertia + Vue/React** — SPA feel with full JS control. Too much infrastructure for a habit tracker.
- **Blade + Alpine.js** *(chosen)* — standard HTTP for all mutations, Alpine for lightweight UI interactions. Easiest to read, debug, and extend.
