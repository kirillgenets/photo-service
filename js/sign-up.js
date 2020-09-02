(() => {
  const URL = "api/signup/";
  const SIGN_IN_URL = "sign-in.html";
  const SUCCESS_STATUS = 201;

  const formElement = document.querySelector(".sign-up-form");
  const errorsListElement = formElement.querySelector(".errors");

  const handleFormSubmit = async (evt) => {
    evt.preventDefault();

    errorsListElement.classList.add("hidden");

    const response = await fetch(URL, {
      method: "POST",
      headers: {
        "Content-Type": "application/json; charset=UTF-8",
      },
      body: JSON.stringify(Object.fromEntries(new FormData(formElement))),
    });

    const result = await response.json();

    if (response.status !== SUCCESS_STATUS) {
      window.utils.renderValidationErrors(result, errorsListElement);
      return;
    }

    window.location.replace(SIGN_IN_URL);
  };

  formElement.addEventListener("submit", handleFormSubmit);
})();
