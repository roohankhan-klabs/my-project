# Coding Agent Instructions: Modern Laravel Stack

This document contains instructions for you, the coding agent, to ensure your generated code and architectural decisions are up to date with the latest versions of the Laravel ecosystem (Laravel 12, Livewire 4, Filament PHP v5, and Laravel Nova 5). Always refer to these guidelines before making modifications.

## Laravel 12
- **Application Structure:** Continues the streamlined and minimalistic default structure (no `app/Console`, `app/Exceptions`, etc. by default). Configuration is primarily in `bootstrap/app.php` and `.env`.
- **Starter Kits:** Prioritize modern starter kits. For frontend-heavy apps, use Inertia 2 with React/Vue/Svelte, TypeScript, shadcn/ui, and Tailwind CSS. For Livewire apps, utilize the Flux UI component library and Laravel Volt.
- **Performance & Eloquent:** Leverage automatic eager loading (introduced in 12.8) to mitigate N+1 query problems. Use the new advanced query builder methods like `NestedWhere()`.
- **Routing:** Utilize the new URI helper for cleaner URL manipulation. Remember that `api.php` and `channels.php` are opt-in. API resources are grouped by auto-discovery (no explicit `ResourceCollection` needed).

## Livewire 4
- **Single-File Components (SFCs):** Favor Single-File Components (`.wire.php`) which combine PHP logic, Blade templates, and JavaScript in one file for an improved developer experience.
- **Performance & Islands:** Livewire 4 uses the Blaze performance engine. Utilize "Islands" to isolate regions of a component that can update independently, avoiding full re-renders. `wire:model.live` and `wire:poll` now run in parallel.
- **Component Composition:** Fully utilize Blade-like component slots and automatic attribute forwarding. 
- **PHP 8.4 Hooks:** Embrace native PHP 8.4 property hooks (getters/setters) for cleaner code inside components.
- **New Directives:** Use `wire:sort` for drag-and-drop, the `data-loading` attribute for styling loading states, and `@placeholder` for skeleton loaders.

## Filament PHP v5
- **Livewire 4 Synergy:** Filament v5 is built to be fully compatible with Livewire v4's architectural shifts, including support for its scoped styles and scripts.
- **AI & Blueprints:** When designing Filament panels, consider the context of Filament Blueprint for AI-assisted development (best practices, accurate implementation plans).
- **Features inherited from v4.5+:** Utilize the enhanced rich editor (with mentions), image resizing/aspect ratio enforcement, and JavaScript actions for interactions that don't need a server round-trip.
- **UI & Blade Components:** Extensively leverage the internal Blade component library and nested Action modals (which can be triggered from anywhere). 

## Laravel Nova 5
- **Modernized Frontend:** Nova 5 leverages Vue 3.5, Inertia.js 2.x, and Tailwind CSS. Custom fields and tools must adhere to this stack.
- **Tab Panels:** Use Tab Panels to organize fields and relationships cleanly on resource detail and form pages for a better UX.
- **Dependent Fields:** Utilize the integrated dependent field API (e.g., the enhanced `computed` method) rather than hacky frontend overrides.
- **Authorization:** Nova operations now utilize separate Policy classes for dedicated authorization logic to keep standard model policies cleaner.
- **Filtering & Display:** Make use of searchable select filters, Enum support for `Select::options()`, and JSON Repeater fields inline on detail pages.

---
**Agent Note:** Keep this context loaded during your sessions and proactively update deprecated syntaxes (like Laravel 10/11 middlewares or Livewire 2/3's structures) when modifying files in this project.
