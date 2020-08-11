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

  photosListWrapperFragment.append(
    ...PHOTOS_DATA.map(({ name, url }) => {
      const wrapper = photoTemplate.content.cloneNode(true);
      const thumbnailElement = wrapper.querySelector(".photo__thumbnail");
      const titleElement = wrapper.querySelector(".photo__title");

      thumbnailElement.src = url;
      thumbnailElement.alt = name;

      titleElement.value = name;

      return wrapper;
    })
  );

  photosListWrapper.append(photosListWrapperFragment);
};

renderAllPhotos();
