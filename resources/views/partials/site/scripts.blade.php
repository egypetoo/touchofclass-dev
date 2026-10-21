<!-- js -->
<script src="{{ Path::jsPath('bootstrap.bundle.min.js') }}"></script> 
<script src="{{ Path::jsPath('gsap.min.js') }}"></script>
<script src="{{ Path::jsPath('lenis.min.js') }}"></script>
<script src="{{ Path::jsPath('swiper-bundle.min.js') }}"></script>
<script src="{{ Path::jsPath('partials.js') }}"></script>
<script src="{{ Path::jsPath('main.js') }}"></script>
<script>
  (function () {
    var box = document.getElementById("km-lightbox");
    var img = box.querySelector("img");
    var caption = box.querySelector(".km-lightbox-caption");
    var shots = [];
    var index = 0;

    function show(i) {
      index = (i + shots.length) % shots.length;
      img.src = shots[index].src;
      img.alt = shots[index].name;
      caption.textContent = shots[index].name;
      box.hidden = false;
      box.classList.add("is-open");
    }

    document.querySelectorAll(".km-album-photos a").forEach(function (link) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        var slug = link.getAttribute("data-album");
        var album = link.closest(".km-album");
        var name = album.querySelector(".km-album-name").textContent;
        shots = Array.from(album.querySelectorAll("a")).map(function (a) {
          return { src: a.getAttribute("href"), name: name };
        });
        show(Number(link.getAttribute("data-index")) || 0);
      });
    });

    box.querySelector(".km-lightbox-close").addEventListener("click", function () {
      box.classList.remove("is-open");
      box.hidden = true;
    });
    box.querySelector(".km-lightbox-next").addEventListener("click", function () { show(index + 1); });
    box.querySelector(".km-lightbox-prev").addEventListener("click", function () { show(index - 1); });
    box.addEventListener("click", function (e) { if (e.target === box) box.querySelector(".km-lightbox-close").click(); });
    document.addEventListener("keydown", function (e) {
      if (box.hidden) return;
      if (e.key === "Escape") box.querySelector(".km-lightbox-close").click();
      if (e.key === "ArrowLeft") show(index + 1);
      if (e.key === "ArrowRight") show(index - 1);
    });
  })();
</script>

