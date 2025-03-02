
import { capitalizeFirstLetter } from '@/utils/formatting';
import { getActivePinia } from 'pinia';

const importStateData = async (key, state) => {
    const storeName = 'use' + capitalizeFirstLetter(key) + 'Store';
  
    try {
        const module = await import(`../stores/${key}.js`);
        const store = module[storeName]();

        // Get existing state keys
        const storeKeys = Object.keys(store);

        for (const storeKey of storeKeys) {
            if (storeKey === '$reset') continue; // Skip function

            if (state.hasOwnProperty(storeKey)) {
                // If key is in the request, update it
                store[storeKey] = state[storeKey];
            } else if (store.$reset) {
                // If key is NOT in the request, reset it
                store.$reset(storeKey);
            } else {
                // If no $reset method exists, set to null
                store[storeKey] = null;
            }
        }
    } catch (error) {
        console.error(`Error dynamically importing store ${storeName}:`, error);
    }
};

export async function piniaLoader(props) {
    const piniaState = props.pinia ?? props.$pinia ?? null;
    if (!piniaState) {
        console.warn('No pinia state found in props.');
        return;
    }

    try {
        const stateProps = typeof piniaState === 'string' ? JSON.parse(piniaState) : piniaState;
        const requestedModules = new Set(Object.keys(stateProps?.modules ?? {}));

        // Reset stores that are NOT in the request
        const pinia = getActivePinia();
        if (!pinia) {
            return;
        }

        // Reset stores that are NOT in the request
        pinia._s.forEach((store, key) => {
            if (!requestedModules.has(key)) {
                store.$reset();
            }
        });

        // Reset the pinia stores' state that are not in the request
        const promises = Object.entries(stateProps?.modules ?? {}).map(([key, { state } ]) => {
            return importStateData(key, state);
        });

        await Promise.all(promises);
    } catch (error) {
        console.error('Error parsing pinia state:', error);
    }
};
