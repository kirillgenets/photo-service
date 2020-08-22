const URL = "api/signup/";

const formElement = document.querySelector(".sign-up-form");

const sendData = () => {};

const handleFormSubmit = async (evt) => {
  evt.preventDefault();
  console.log(Object.fromEntries(new FormData(formElement)));

  const response = await fetch(URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json; charset=UTF-8",
    },
    body: JSON.stringify(Object.fromEntries(new FormData(formElement))),
  });

  const result = await response.json();

  console.log("handleFormSubmit -> result", result);
};

formElement.addEventListener("submit", handleFormSubmit);
