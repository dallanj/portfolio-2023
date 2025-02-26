
import { capitalizeFirstLetter } from '@/utils/formatting';

const importStateData = async (key, state) => {
    const storeName = 'use' + capitalizeFirstLetter(key) + 'Store';
  
    try {
        const module = await import(`../stores/${key}.js`);
        const store = module[storeName]();

        for (const [storeKey, value] of Object.entries(state)) {
            if (store[storeKey] !== undefined) {
                store[storeKey] = value;
            }
        }
    } catch (error) {
        console.error(`Error dynamically importing store ${storeName}:`, error);
    }
};

export async function piniaLoader(props) {
    let piniaState = props.pinia ?? props.$pinia ?? null;
    
    if (!piniaState) {
        console.warn('No pinia state found in props.');
        return;
    }

    try {
        const stateProps = typeof piniaState === 'string' ? JSON.parse(piniaState) : piniaState;

        const promises = Object.entries(stateProps?.modules ?? {}).map(([key, { state } ]) => {
            return importStateData(key, state);
        });

        await Promise.all(promises);
    } catch (error) {
        console.error('Error parsing pinia state:', error);
    }
};
