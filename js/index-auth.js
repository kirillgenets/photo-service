(() => {
  const UPLOAD_PHOTO_URL = "api/photo/";

  const PHOTOS_DATA = [
    {
      id: 0,
      name: "Просто фотка",
      owner_id: 0,
      users: [0, 1, 2],
      url: "img/1.jpg",
    },
    {
      id: 1,
      name: "Просто фотка",
      owner_id: 0,
      users: [0, 1, 2],
      url: "img/_21.jpg",
    },
    {
      id: 2,
      name: "Просто фотка",
      owner_id: 0,
      users: [0, 1, 2],
      url: "img/13.jpg",
    },
    {
      id: 3,
      name: "Просто фотка",
      owner_id: 0,
      users: [0, 1, 2],
      url: "img/images.jpg",
    },
    {
      id: 4,
      name: "Просто фотка",
      owner_id: 0,
      users: [0, 1, 2],
      url: "img/kazan1.jpg",
    },
  ];

  const photoTemplate = document.querySelector("#photo");
  const photosListWrapper = document.querySelector(".photos__list");
  const uploadPhotoInput = document.querySelector(".photos__upload-button");

  const handleFileReaderLoadEnded = async (evt) => {
    const response = await fetch(UPLOAD_PHOTO_URL, {
      method: "POST",
      headers: {
        "Content-Type": "multipart/form-data; charset=utf-8;",
      },
      body: JSON.stringify({ photo: evt.target.result }),
    });

    const result = await response.text();
    console.log("handleFileReaderLoadEnded -> result", result);
    // body: JSON.stringify({
    //   photo: evt.target.result,
    // }),
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

    const response = await fetch(UPLOAD_PHOTO_URL, options);

    const result = await response.text();
    console.log("handleFileReaderLoadEnded -> result", result);
    // const reader = new FileReader();

    // reader.readAsDataURL(evt.target.files[0]);
    // reader.addEventListener("loadend", handleFileReaderLoadEnded);
  };

  const renderAllPhotos = () => {
    const photosListWrapperFragment = document.createDocumentFragment();

    const handlePhotoDeleteButtonClick = (evt) => {
      console.log("handlePhotoDeleteButtonClick -> evt", evt);
    };

    const handlePhotoEditButtonClick = (evt) => {
      console.log("handlePhotoEditButtonClick -> evt", evt);
    };

    const handlePhotoShareButtonClick = (evt) => {
      console.log("handlePhotoShareButtonClick -> evt", evt);
    };

    const handlePhotoTitleChange = (evt) => {
      console.log("handlePhotoTitleChange -> evt", evt);
    };

    const setPhotoEventListeners = (element) => {
      element
        .querySelector(".photo__button--delete")
        .addEventListener("click", handlePhotoDeleteButtonClick);
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

    photosListWrapperFragment.append(
      ...PHOTOS_DATA.map(({ name, url }) => {
        const wrapper = photoTemplate.content.cloneNode(true);
        const thumbnailElement = wrapper.querySelector(".photo__thumbnail");
        const titleElement = wrapper.querySelector(".photo__title");

        thumbnailElement.src = url;
        thumbnailElement.alt = name;

        titleElement.value = name;

        setPhotoEventListeners(wrapper);

        return wrapper;
      })
    );

    photosListWrapper.append(photosListWrapperFragment);
  };

  renderAllPhotos();

  uploadPhotoInput.addEventListener("change", handleUploadPhotoInputChange);
})();
