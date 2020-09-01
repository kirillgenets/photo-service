(() => {
  const SIGN_IN_URL = "sign-in.html";
  const PHOTOS_URL = "api/photo/";
  const USERS_URL = "api/user/";
  const POST_SUCCESS_STATUS = 201;
  const GET_SUCCESS_STATUS = 200;
  const PATCH_SUCCESS_STATUS = 200;
  const DELETE_SUCCESS_STATUS = 204;
  const PHOTOS_PER_PAGE = 6;
  const HASHTAGS_ALTERNATIVE_TEXT = "Нет хэштегов";

  const photoTemplate = document.querySelector("#photo");
  const sharePhotoUserTemplate = document.querySelector("#share-photo-user");

  const photosWrapper = document.querySelector(".photos");

  const uploadPhotoModal = document.querySelector(".upload-photo");
  const uploadPhotoForm = uploadPhotoModal.querySelector(".upload-photo__form");
  const uploadPhotoFormErrors = uploadPhotoForm.querySelector(".form__errors");

  const updatePhotoModal = document.querySelector(".update-photo");
  const updatePhotoForm = updatePhotoModal.querySelector(".update-photo__form");
  const updatePhotoFormErrors = updatePhotoForm.querySelector(".form__errors");

  const sharePhotoModal = document.querySelector(".share-modal");
  const sharePhotoForm = sharePhotoModal.querySelector(".share-modal__form");
  const sharePhotoUserSearchInput = sharePhotoForm.querySelector(
    ".share-modal__search"
  );
  const sharePhotoUserSearchButton = sharePhotoForm.querySelector(
    ".share-modal__search-button"
  );

  const sharePhotoUsersList = sharePhotoForm.querySelector(
    ".share-modal__users"
  );

  const photosListWrapper = photosWrapper.querySelector(".photos__list");
  const uploadPhotoInput = photosWrapper.querySelector(
    ".photos__upload-button"
  );
  const paginationWrapper = photosWrapper.querySelector(".photos__pagination");

  const exitButton = document.querySelector(".header__exit-button");

  const authData = JSON.parse(localStorage.getItem("auth"));
  let currentPage = 1;

  if (!authData) window.location.replace(SIGN_IN_URL);

  const handlePhotoDeleteButtonClick = (id) => async (evt) => {
    const response = await fetch(
      `${PHOTOS_URL}/${id}/?owner_id=${authData.id}`,
      {
        method: "DELETE",
        headers: {
          ["Authorization"]: authData.token,
        },
      }
    );

    if (response.status !== DELETE_SUCCESS_STATUS) return;
    evt.target.parentNode.remove();
    renderPagination();
  };

  const renderUpdatePhotoModal = (id) => {
    const handleFormSubmit = async (evt) => {
      evt.preventDefault();
      updatePhotoFormErrors.innerHTML = "";

      const formData = new FormData(evt.target);

      formData.append("owner_id", authData.id);
      formData.append("id", id);

      const options = {
        method: "POST",
        headers: {
          "Content-Type": "application/json; charset=utf-8;",
          ["Authorization"]: authData.token,
        },
        body: JSON.stringify(Object.fromEntries(formData)),
      };

      const response = await fetch(`${PHOTOS_URL}/${id}`, options);
      const result = await response.json();

      if (response.status !== PATCH_SUCCESS_STATUS) {
        window.utils.renderValidationErrors(result, updatePhotoFormErrors);
        return;
      }

      renderAllPhotos();
      renderPagination();

      updatePhotoModal.classList.add("hidden");
    };

    updatePhotoModal.classList.remove("hidden");
    updatePhotoForm.addEventListener("submit", handleFormSubmit);
  };

  const handlePhotoEditButtonClick = (id) => () => {
    renderUpdatePhotoModal(id);
  };

  const renderSharePhotoModal = (id) => {
    const renderUser = ({ first_name: firstName, surname, id }) => {
      const userElement = sharePhotoUserTemplate.content.cloneNode(true);

      userElement.querySelector(".share-modal__user-checkbox").value = id;
      userElement.querySelector(
        ".share-modal__user-name"
      ).textContent = `${firstName} ${surname}`;

      return userElement;
    };

    const renderUsers = async () => {
      const response = await fetch(`${USERS_URL}`, {
        method: "GET",
        headers: {
          ["Authorization"]: authData.token,
        },
      });

      const result = await response.json();

      if (response.status !== GET_SUCCESS_STATUS) return;

      sharePhotoUsersList.innerHTML = "";
      sharePhotoUsersList.append(...result.map(renderUser));
    };

    const handleSharePhotoUserSearchButtonClick = async () => {
      const search = sharePhotoUserSearchInput.value;
      const response = await fetch(`${USERS_URL}/?search=${search}`, {
        method: "GET",
        headers: {
          ["Authorization"]: authData.token,
        },
      });

      const result = await response.json();
      if (response.status !== GET_SUCCESS_STATUS) return;
    };

    const handleSharePhotoFormSubmit = () => {
      // const response = await fetch(`${USERS_URL}/${authData.id}/share`, {
      //   method: "POST",
      //   body: {},
      // });
      // console.log(
      //   "handlePhotoShareButtonClick -> response",
      //   await response.text()
      // );
    };
    renderUsers();

    sharePhotoModal.classList.remove("hidden");
    sharePhotoForm.addEventListener("submit", handleSharePhotoFormSubmit);
    sharePhotoUserSearchButton.addEventListener(
      "click",
      handleSharePhotoUserSearchButtonClick
    );
  };

  const handlePhotoShareButtonClick = (id) => async () => {
    renderSharePhotoModal(id);
  };

  const handlePhotoTitleChange = (evt) => {
    console.log("handlePhotoTitleChange -> evt", evt);
  };

  const setPhotoEventListeners = (element, id) => {
    element
      .querySelector(".photo__button--delete")
      .addEventListener("click", handlePhotoDeleteButtonClick(id));
    element
      .querySelector(".photo__button--edit")
      .addEventListener("click", handlePhotoEditButtonClick(id));
    element
      .querySelector(".photo__button--share")
      .addEventListener("click", handlePhotoShareButtonClick(id));
    element
      .querySelector(".photo__title")
      .addEventListener("change", handlePhotoTitleChange);
  };

  const renderPhoto = ({ id, name, url, hashtags }) => {
    const wrapper = photoTemplate.content.cloneNode(true);
    const thumbnailElement = wrapper.querySelector(".photo__thumbnail");
    const titleElement = wrapper.querySelector(".photo__title");
    const hashtagsElement = wrapper.querySelector(".photo__hashtags");

    thumbnailElement.src = url;
    thumbnailElement.alt = name;

    titleElement.textContent = name;
    hashtagsElement.textContent = hashtags || HASHTAGS_ALTERNATIVE_TEXT;

    setPhotoEventListeners(wrapper, id);

    return wrapper;
  };

  const renderUploadPhotoModal = (photo) => {
    const destroyModal = () => {
      uploadPhotoInput.value = "";
      uploadPhotoModal.classList.add("hidden");
    };

    const handleFormSubmit = async (evt) => {
      evt.preventDefault();
      uploadPhotoFormErrors.innerHTML = "";

      const formData = new FormData(evt.target);

      formData.append("photo", photo);
      formData.append("id", authData.id);

      const options = {
        method: "POST",
        headers: {
          "Content-Type": "multipart/form-data; charset=utf-8;",
          ["Authorization"]: authData.token,
        },
        body: formData,
      };

      delete options.headers["Content-Type"];

      const response = await fetch(PHOTOS_URL, options);
      const result = await response.json();

      if (response.status !== POST_SUCCESS_STATUS) {
        window.utils.renderValidationErrors(result, uploadPhotoFormErrors);
        return;
      }

      renderAllPhotos();
      renderPagination();
      destroyModal();
    };

    uploadPhotoModal.classList.remove("hidden");
    uploadPhotoForm.addEventListener("submit", handleFormSubmit);
  };

  const handleUploadPhotoInputChange = (evt) => {
    renderUploadPhotoModal(evt.target.files[0]);
  };

  const handlePaginationItemClick = (evt) => {
    evt.preventDefault();

    currentPage = evt.target.dataset.id;
    renderAllPhotos();
  };

  const renderPagination = async () => {
    paginationWrapper.innerHTML = "";

    const response = await fetch(`${PHOTOS_URL}/?owner_id=${authData.id}`, {
      method: "GET",
      headers: {
        ["Authorization"]: authData.token,
      },
    });
    const result = await response.json();
    const photosCount = result.length;
    const pagesCount = Math.ceil(photosCount / PHOTOS_PER_PAGE);

    if (!photosCount) return;

    const paginationWrapperFragment = document.createDocumentFragment();

    for (let i = 1; i <= pagesCount; i++) {
      const paginationItemElement = document.createElement("a");

      paginationItemElement.classList.add("photos__pagination-item");
      paginationItemElement.href = i;
      paginationItemElement.textContent = i;
      paginationItemElement.setAttribute("data-id", i);

      paginationItemElement.addEventListener(
        "click",
        handlePaginationItemClick
      );

      paginationWrapperFragment.append(paginationItemElement);
    }

    paginationWrapper.append(paginationWrapperFragment);
  };

  const renderAllPhotos = async () => {
    photosListWrapper.innerHTML = "";

    const response = await fetch(
      `${PHOTOS_URL}/?page=${currentPage}&owner_id=${authData.id}`,
      {
        method: "GET",
        headers: {
          ["Authorization"]: authData.token,
        },
      }
    );
    const result = await response.json();

    const data = response.status === GET_SUCCESS_STATUS ? result : [];

    const photosListWrapperFragment = document.createDocumentFragment();

    photosListWrapperFragment.append(...data.map(renderPhoto));
    photosListWrapper.append(photosListWrapperFragment);
  };

  const handleExitButtonClick = () => {
    localStorage.removeItem("auth");
    window.location.replace(SIGN_IN_URL);
  };

  renderAllPhotos();
  renderPagination();

  uploadPhotoInput.addEventListener("change", handleUploadPhotoInputChange);
  exitButton.addEventListener("click", handleExitButtonClick);
})();
