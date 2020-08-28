(() => {
  const PHOTOS_URL = "api/photo/";
  const USERS_URL = "api/user/";
  const POST_SUCCESS_STATUS = 201;
  const GET_SUCCESS_STATUS = 200;
  const DELETE_SUCCESS_STATUS = 204;
  const PHOTOS_PER_PAGE = 6;

  const photoTemplate = document.querySelector("#photo");
  const paginationItemTemplate = document.querySelector("#pagination-item");

  const photosWrapper = document.querySelector(".photos");
  const photosListWrapper = photosWrapper.querySelector(".photos__list");
  const uploadPhotoInput = photosWrapper.querySelector(
    ".photos__upload-button"
  );
  const paginationWrapper = photosWrapper.querySelector(".photos__pagination");

  let currentPage = 1;

  const handlePhotoDeleteButtonClick = (id) => async (evt) => {
    const response = await fetch(`${PHOTOS_URL}/${id}`, {
      method: "DELETE",
    });

    if (response.status !== DELETE_SUCCESS_STATUS) return;
    evt.target.parentNode.remove();
  };

  const handlePhotoEditButtonClick = (evt) => {
    console.log("handlePhotoEditButtonClick -> evt", evt);
  };

  const handlePhotoShareButtonClick = (evt) => {
    // const response = await fetch(`${USERS_URL}/${id}/share`, {
    //   method: "POST",
    //   body: {}
    // });

    // if (response.status !== DELETE_SUCCESS_STATUS) return;
    // evt.target.remove();
    console.log("handlePhotoShareButtonClick -> evt", evt);
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
      .addEventListener("click", handlePhotoEditButtonClick);
    element
      .querySelector(".photo__button--share")
      .addEventListener("click", handlePhotoShareButtonClick);
    element
      .querySelector(".photo__title")
      .addEventListener("change", handlePhotoTitleChange);
  };

  const renderPhoto = ({ id, name, url }) => {
    const wrapper = photoTemplate.content.cloneNode(true);
    const thumbnailElement = wrapper.querySelector(".photo__thumbnail");
    const titleElement = wrapper.querySelector(".photo__title");

    thumbnailElement.src = url;
    thumbnailElement.alt = name;

    titleElement.value = name;

    setPhotoEventListeners(wrapper, id);

    return wrapper;
  };

  const handleUploadPhotoInputChange = async (evt) => {
    const formData = new FormData();
    const authData = JSON.parse(localStorage.getItem("auth"));

    formData.append("photo", evt.target.files[0]);
    formData.append("id", authData.id);

    const options = {
      method: "POST",
      headers: {
        "Content-Type": "multipart/form-data; charset=utf-8;",
      },
      body: formData,
    };

    delete options.headers["Content-Type"];

    const response = await fetch(PHOTOS_URL, options);
    const result = await response.json();

    if (response.status === POST_SUCCESS_STATUS) {
      photosListWrapper.append(renderPhoto(result));
    }
  };

  const handlePaginationItemClick = (evt) => {
    evt.preventDefault();

    currentPage = evt.target.dataset.id;
    renderAllPhotos();
  };

  const renderPagination = async () => {
    const response = await fetch(PHOTOS_URL, { method: "GET" });
    const result = await response.json();
    const photosCount = Math.ceil(result.length / PHOTOS_PER_PAGE);

    const paginationWrapperFragment = document.createDocumentFragment();

    for (let i = 1; i <= photosCount; i++) {
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

    const response = await fetch(`${PHOTOS_URL}/?page=${currentPage}`, {
      method: "GET",
    });
    const result = await response.json();

    const data =
      response.status === GET_SUCCESS_STATUS
        ? result.filter(
            ({ owner_id: ownerId }) =>
              ownerId === JSON.parse(localStorage.getItem("auth")).id
          )
        : [];

    const photosListWrapperFragment = document.createDocumentFragment();

    photosListWrapperFragment.append(...data.map(renderPhoto));
    photosListWrapper.append(photosListWrapperFragment);
  };

  renderAllPhotos();
  renderPagination();

  uploadPhotoInput.addEventListener("change", handleUploadPhotoInputChange);
})();
