const URL = "api/signup/";
const AUTH_URL = "index-auth.html";
const SUCCESS_STATUS = 201;

const formElement = document.querySelector(".sign-up-form");
const errorsListElement = formElement.querySelector(".form__errors");

const showErrors = (messages) => {
  errorsListElement.classList.remove("hidden");
  errorsListElement.append(
    ...Object.values(messages).map((message) => {
      const errorElement = document.createElement("li");
      errorElement.classList.add("form__error");
      errorElement.textContent = message;

      return errorElement;
    })
  );
};

const handleFormSubmit = async (evt) => {
  evt.preventDefault();

  errorsListElement.classList.add("hidden");
  errorsListElement.innerHTML = "";

  const response = await fetch(URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json; charset=UTF-8",
    },
    body: JSON.stringify(Object.fromEntries(new FormData(formElement))),
  });

  const result = await response.json();

  if (response.status !== SUCCESS_STATUS) {
    showErrors(result);
    return;
  }

  window.location.replace(AUTH_URL);
};

formElement.addEventListener("submit", handleFormSubmit);
