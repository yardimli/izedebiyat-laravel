<div class="col-lg-12 col-md-12 col-sm-12" style="	margin-bottom:10px;">
	<div class="pagination2__wrapper">
		<ul class="pagination2" id="pagination_bar_bottom">
		</ul>
	</div>
</div>


<style>
  button_link {
    overflow: visible;
  }

  .button_link {
    font: inherit;
    margin: 0;
    text-decoration: none;
  }

  .button_link {
    text-transform: none;
  }

  .pagination2:after, .pagination2:before {
    content: "";
    display: table;
  }

  .pagination2:after {
    clear: both;
  }

  .pagination2__wrapper {
    background: -webkit-gradient(linear, left top, right top, from(rgba(255, 255, 255, 0)), color-stop(17%, white), color-stop(83%, white), to(rgba(255, 255, 255, 0)));
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, white 17%, white 83%, rgba(255, 255, 255, 0) 100%);
    height: 50px;
    text-align: center;
  }

  .pagination2__wrapper:before, .pagination2__wrapper:after {
    background: -webkit-gradient(linear, left top, right top, from(rgba(0, 0, 0, 0)), color-stop(17%, rgba(0, 0, 0, 0.1)), color-stop(83%, rgba(0, 0, 0, 0.1)), to(rgba(0, 0, 0, 0)));
    background: linear-gradient(to right, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.1) 17%, rgba(0, 0, 0, 0.1) 83%, rgba(0, 0, 0, 0) 100%);
    content: "";
    height: 1px;
    left: 0;
    position: absolute;
    width: 100%;
  }

  .pagination2__wrapper:before {
    top: -1px;
  }

  .pagination2__wrapper:after {
    bottom: -1px;
  }

  @-webkit-keyframes hoverAnimation {
    from {
      opacity: 1;
    }
    to {
      opacity: 0;
    }
  }

  @keyframes hoverAnimation {
    from {
      opacity: 1;
    }
    to {
      opacity: 0;
    }
  }

  .pagination2 {
    display: inline-block;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .pagination2 li {
    display: block;
    float: left;
    padding: 5px;
  }

  .pagination2 li:first-child {
    border: none;
  }

  .pagination2 .button_link,
  .pagination2 span {
    background: none;
    border: none;
    border-radius: 50%;
    box-sizing: border-box;
    color: rgba(0, 0, 0, 0.6);
    display: block;
    font-size: 16px;
    height: 40px;
    line-height: 40px;
    padding: 0;
  }

  .pagination2 .button_link {
    outline: none;
    position: relative;
    -webkit-transition: all 170ms linear;
    transition: all 170ms linear;
  }

  .pagination2 .button_link:before {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 50%;
    content: "";
    cursor: pointer;
    height: 0;
    left: 50%;
    opacity: 0;
    position: absolute;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    -webkit-transition: all 170ms linear;
    transition: all 170ms linear;
    top: 50%;
    width: 0;
  }

  .pagination2 .button_link:hover:not(.active) {
    color: black;
  }

  .pagination2 .button_link:hover:not(.active):before {
    -webkit-animation: hoverAnimation 510ms linear forwards;
    animation: hoverAnimation 510ms linear forwards;
    height: 40px;
    width: 40px;
  }

  .pagination2 .button_link.active {
    background: rgba(0, 0, 0, 0.1);
    color: black;
  }

  .pagination2 .prev,
  .pagination2 .next {
    font-size: 14px;
  }

  [data-bs-theme=dark] button_link {
      overflow: visible;
  }

  [data-bs-theme=dark] .button_link {
      font: inherit;
      margin: 0;
      text-decoration: none;
  }

  [data-bs-theme=dark] .button_link {
      text-transform: none;
  }

  [data-bs-theme=dark] .pagination2:after,
  [data-bs-theme=dark] .pagination2:before {
      content: "";
      display: table;
  }

  [data-bs-theme=dark] .pagination2:after {
      clear: both;
  }

  [data-bs-theme=dark] .pagination2__wrapper {
      background: linear-gradient(to right, rgba(0, 0, 0, 0) 0%, #212529 17%, #212529 83%, rgba(0, 0, 0, 0) 100%);
      height: 50px;
      text-align: center;
  }

  [data-bs-theme=dark] .pagination2__wrapper:before,
  [data-bs-theme=dark] .pagination2__wrapper:after {
      background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.1) 17%, rgba(255, 255, 255, 0.1) 83%, rgba(255, 255, 255, 0) 100%);
      content: "";
      height: 1px;
      left: 0;
      position: absolute;
      width: 100%;
  }

  [data-bs-theme=dark] .pagination2__wrapper:before {
      top: -1px;
  }

  [data-bs-theme=dark] .pagination2__wrapper:after {
      bottom: -1px;
  }

  @keyframes hoverAnimation {
      from {
          opacity: 1;
      }
      to {
          opacity: 0;
      }
  }

  [data-bs-theme=dark] .pagination2 {
      display: inline-block;
      list-style: none;
      margin: 0;
      padding: 0;
  }

  [data-bs-theme=dark] .pagination2 li {
      display: block;
      float: left;
      padding: 5px;
  }

  [data-bs-theme=dark] .pagination2 li:first-child {
      border: none;
  }

  [data-bs-theme=dark] .pagination2 .button_link,
  [data-bs-theme=dark] .pagination2 span {
      background: none;
      border: none;
      border-radius: 50%;
      box-sizing: border-box;
      color: rgba(255, 255, 255, 0.6);
      display: block;
      font-size: 16px;
      height: 40px;
      line-height: 40px;
      padding: 0;
  }

  [data-bs-theme=dark] .pagination2 .button_link {
      outline: none;
      position: relative;
      transition: all 170ms linear;
  }

  [data-bs-theme=dark] .pagination2 .button_link:before {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      content: "";
      cursor: pointer;
      height: 0;
      left: 50%;
      opacity: 0;
      position: absolute;
      transform: translate(-50%, -50%);
      transition: all 170ms linear;
      top: 50%;
      width: 0;
  }

  [data-bs-theme=dark] .pagination2 .button_link:hover:not(.active) {
      color: white;
  }

  [data-bs-theme=dark] .pagination2 .button_link:hover:not(.active):before {
      animation: hoverAnimation 510ms linear forwards;
      height: 40px;
      width: 40px;
  }

  [data-bs-theme=dark] .pagination2 .button_link.active {
      background: rgba(255, 255, 255, 0.1);
      color: white;
  }

  [data-bs-theme=dark] .pagination2 .prev,
  [data-bs-theme=dark] .pagination2 .next {
      font-size: 14px;
  }

</style>

<script>
  // Returns an array of maxLength (or less) page numbers
  // where a 0 in the returned array denotes a gap in the series.
  // Parameters:
  //   totalPages:     total number of pages
  //   page:           current page
  //   maxLength:      maximum size of returned array
  function getPageList(totalPages, page, maxLength) {
    if (maxLength < 5) throw "maxLength must be at least 5";

    function range(start, end) {
      return Array.from(Array(end - start + 1), (_, i) => i + start);
    }

    var sideWidth = maxLength < 9 ? 1 : 2;
    var leftWidth = (maxLength - sideWidth * 2 - 3) >> 1;
    var rightWidth = (maxLength - sideWidth * 2 - 2) >> 1;
    if (totalPages <= maxLength) {
      // no breaks in list
      return range(1, totalPages);
    }
    if (page <= maxLength - sideWidth - 1 - rightWidth) {
      // no break on left of page
      return range(1, maxLength - sideWidth - 1)
        .concat(0, range(totalPages - sideWidth + 1, totalPages));
    }
    if (page >= totalPages - sideWidth - 1 - rightWidth) {
      // no break on right of page
      return range(1, sideWidth)
        .concat(0, range(totalPages - sideWidth - 1 - rightWidth - leftWidth, totalPages));
    }
    // Breaks on both sides
    return range(1, sideWidth)
      .concat(0, range(page - leftWidth, page + rightWidth),
        0, range(totalPages - sideWidth + 1, totalPages));
  }

  function showPage(whichPage,totalPages,paginationSize) {
    if (whichPage < 0 || whichPage > totalPages) return false;
    currentPage = whichPage;

    // Replace the navigation items (not prev/next):
    $("#pagination_bar li").slice(1, -1).remove();

    getPageList(totalPages, currentPage, paginationSize).forEach(item => {

      //   <!--			<li>-->
      // <!--				<button title="page 8">8</button>-->
      //     <!--			</li>-->

      $("<li>").addClass("page-item")
        .addClass(item ? "current-page" : "disabled")
        .append(
          $("<a>").addClass("button_link").toggleClass("active", item === currentPage).attr({
            href: NaviationNextPageURL + "" + (item + 0), style: "min-width:40px"
          }).text(item || "...")
        ).insertBefore("#next-page");


      $("<li>").addClass("page-item")
        .addClass(item ? "current-page" : "disabled")
        .append(
          $("<a>").addClass("button_link").toggleClass("active", item === currentPage).attr({
            href: NaviationNextPageURL + "" + (item + 0), style: "min-width:40px"
          }).text(item || "...")
        ).insertBefore("#next-page_bottom");
    });

    // Disable prev/next when at first/last page:
    if (currentPage === 1) {
      $("#previous-page .button_link").attr({href:NaviationNextPageURL+"1"});
      $("#previous-page_bottom .button_link").attr({href:NaviationNextPageURL+"1"});
    }

    if (currentPage === totalPages) {
      $("#next-page .button_link").attr({href:NaviationNextPageURL+(totalPages)});
      $("#next-page_bottom .button_link").attr({href:NaviationNextPageURL+(totalPages)});
    }

    $("#previous-page").toggleClass("disabled", currentPage === 1);
    $("#next-page").toggleClass("disabled", currentPage === totalPages);

    $("#previous-page_bottom").toggleClass("disabled", currentPage === 1);
    $("#next-page_bottom").toggleClass("disabled", currentPage === totalPages);
    return true;
  }

  $(document).ready(function () {
    var paginationSize = 8;
    if ($(window).width() <= 768) {
      paginationSize = 5;
    }

    // Include the prev/next buttons:
    if ($(window).width() <= 768) {

      $("#pagination_bar").append(
        $("<li>").addClass("page-item").attr({id: "previous-page"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage - 1)
          }).text("<")
        ),
        $("<li>").addClass("page-item").attr({id: "next-page"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage + 1)
          }).text(">")
        )
      );

      $("#pagination_bar_bottom").append(
        $("<li>").addClass("page-item").attr({id: "previous-page_bottom"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage - 1)
          }).text("<")
        ),
        $("<li>").addClass("page-item").attr({id: "next-page_bottom"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage + 1)
          }).text(">")
        )
      );

    }
    else {

      $("#pagination_bar").append(
        $("<li>").addClass("page-item").attr({id: "previous-page"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage - 1), style: "min-width:40px"
          }).text("Önceki")
        ),
        $("<li>").addClass("page-item").attr({id: "next-page"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage + 1), style: "min-width:40px"
          }).text("Sonraki")
        )
      );

      $("#pagination_bar_bottom").append(
        $("<li>").addClass("page-item").attr({id: "previous-page_bottom"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage - 1), style: "min-width:40px"
          }).text("Önceki")
        ),
        $("<li>").addClass("page-item").attr({id: "next-page_bottom"}).append(
          $("<a>").addClass("button_link").attr({
            href: NaviationNextPageURL + "" + (currentPage + 1), style: "min-width:40px"
          }).text("Sonraki")
        )
      );

    }

    //Show the page links
    showPage(currentPage,totalPages,paginationSize);
  });

</script>
