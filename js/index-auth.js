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

const renderAllPhotos = () => {
  const photosListWrapperFragment = document.createDocumentFragment();

  const handlePhotoDeleteButtonClick = (evt) => {
    console.log("handlePhotoDeleteButtonClick -> evt", evt);
  };

  const handlePhotoEditButtonClick = (evt) => {
    console.log("handlePhotoEditButtonClick -> evt", evt);
  };

  const setPhotoEventListeners = (element) => {
    element
      .querySelector(".photo__button--delete")
      .addEventListener("click", handlePhotoDeleteButtonClick);
    element
      .querySelector(".photo__button--edit")
      .addEventListener("click", handlePhotoEditButtonClick);
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
