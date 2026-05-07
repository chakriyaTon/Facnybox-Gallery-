// Fancybox — bind all gallery links on page ready
document.addEventListener("DOMContentLoaded", function () {

  Fancybox.bind("[data-fancybox]", {
    Thumbs: false,
    Toolbar: {
      display: ["close"],
    },
  });

});
