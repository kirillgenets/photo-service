(() => {
  window.utils = {
    renderValidationErrors: (messages, errorsListElement) => {
      errorsListElement.innerHTML = "";
      errorsListElement.classList.remove("hidden");
      errorsListElement.append(
        ...Object.values(messages).map((message) => {
          const errorElement = document.createElement("li");
          errorElement.classList.add("error");
          errorElement.textContent = message;

          return errorElement;
        })
      );
    },
  };
})();
