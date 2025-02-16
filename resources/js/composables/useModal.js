import { toRefs, reactive } from 'vue';

const modalState = reactive({
    activeModal: null,
    modalProps: {},
    resolve: null,
});

export function useModal() {
  const openModal = (modalName, props = {}) => {
      return new Promise((resolve) => {
          modalState.activeModal = modalName;
          modalState.modalProps = props;
          // Store the resolver function
          modalState.resolve = resolve;
        //   console.log(modalState);
      });
  };
  const closeModal = (result) => {
      if (modalState.resolve) {
          // Resolve the promise with the result
          modalState.resolve(result);
      }
      modalState.activeModal = null;
      modalState.modalProps = {};
      modalState.resolve = null;
  };

  return {
    ...toRefs(modalState),
    openModal,
    closeModal
  };
}

