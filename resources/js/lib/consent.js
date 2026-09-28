/**
 * Cookie consent state for the optional banner (config/site.php consent).
 * Read $store.consent.optional before loading any non-essential script.
 */
export function consentStore(storageKey = 'consent') {
    return {
        decided: false,
        optional: false,

        init() {
            try {
                const raw = window.localStorage.getItem(storageKey);

                if (! raw) {
                    return;
                }

                const parsed = JSON.parse(raw);

                if (typeof parsed?.optional === 'boolean') {
                    this.optional = parsed.optional;
                    this.decided = parsed.decided !== false;
                }
            } catch {
                // Blocked or corrupt storage: keep the undecided defaults.
            }
        },

        persist() {
            window.localStorage.setItem(storageKey, JSON.stringify({
                optional: this.optional,
                decided: this.decided,
            }));
        },

        acceptAll() {
            this.optional = true;
            this.decided = true;
            this.persist();
        },

        essentialOnly() {
            this.optional = false;
            this.decided = true;
            this.persist();
        },

        reopen() {
            this.decided = false;
            this.persist();
        },
    };
}
