(() => {
  window.utils = {
    renderValidationErrors: (messages, errorsListElement) => {
      errorsListElement.classList.remove("hidden");
      errorsListElement.append(
        ...Object.values(messages).map((message) => {
          const errorElement = document.createElement("li");
          errorElement.classList.add("form__error");
          errorElement.textContent = message;

          return errorElement;
        })
      );
    },
  };
})();
