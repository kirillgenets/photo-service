(() => {
  const URL = "api/login/";
  const AUTH_URL = "index-auth.html";
  const SUCCESS_STATUS = 200;

  const formElement = document.querySelector(".sign-in-form");
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

    localStorage.setItem("auth", JSON.stringify(result));

    if (response.status !== SUCCESS_STATUS) {
      window.utils.renderValidationErrors(result, errorsListElement);
      return;
    }

    window.location.replace(AUTH_URL);
  };

  formElement.addEventListener("submit", handleFormSubmit);
})();
