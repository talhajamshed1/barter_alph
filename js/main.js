
$('.search-btn-top,#search-pop-close').click(function(e){
	e.preventDefault();

	$('.search-pop').toggleClass('active');
})	
$('#category-drop,#category-close').click(function(e){
  e.preventDefault();

  $('.side-category-menu').toggleClass('active');
})  

var lastScrollTop = 0;
$(window).scroll(function(event){
   var st = $(this).scrollTop();
   $('.search-pop').removeClass('active');
   if (st > lastScrollTop){
       $('header').addClass('scroll');
   } else {
     $('header').removeClass('scroll');
   }
   lastScrollTop = st;
});

// height member-item
var elementHeights2 = $('.content-sec-btm-grid-tile-inner p').map(function () {
return $(this).height();
}).get();
var maxHeight2 = Math.max.apply(null, elementHeights2);
$('.content-sec-btm-grid-tile-inner p').height(maxHeight2);

$('.itemSlider').slick({
      dots:false,
      arrows:true,
      fade:false,
      autoplay:false,
      slidesToShow: 4,
      slidesToScroll: 1,
       prevArrow: '<div class="slick-arrow slick-prev"><i class="flaticon-left-arrow"></i></div>',
            nextArrow: '<div class="slick-arrow slick-next"><i class="flaticon-next"></i></div>',
      responsive: [
        {
          breakpoint: 767,
          settings: {
            
            variableWidth: true,
            infinite: true,
          }
        },]

    });

$('.productdetailpopup').on('shown.bs.modal', function (e) {
  var modalid = parseInt($(this).attr('id').replace(/[^0-9\.]/g, ''), 10);
  // alert(modalid);
  $("#ProductImg"+modalid).elevateZoom({
		cursor: "crosshair",
		easing : true, 
		gallery:'product_item_gallery'+modalid,
		zoomType: "inner",
		galleryActiveClass: "active"
  }); 

	$('#product_item_gallery'+modalid).slick({
      dots:false,
      arrows:true,
      fade:false,
      autoplay:false,
      slidesToShow: 4,
      slidesToScroll: 1,
       prevArrow: '<div class="slick-arrow slick-prev"><i class="flaticon-left-arrow"></i></div>',
            nextArrow: '<div class="slick-arrow slick-next"><i class="flaticon-next"></i></div>'

    });
$("#ProductImg0"+modalid).elevateZoom({
    cursor: "crosshair",
    easing : true, 
    gallery:'product_item_gallery'+modalid,
    zoomType: "inner",
    galleryActiveClass: "active"
  }); 

  $('#product_item_gallery0'+modalid).slick({
      dots:false,
      arrows:true,
      fade:false,
      autoplay:false,
      slidesToShow: 4,
      slidesToScroll: 1,
       prevArrow: '<div class="slick-arrow slick-prev"><i class="flaticon-left-arrow"></i></div>',
            nextArrow: '<div class="slick-arrow slick-next"><i class="flaticon-next"></i></div>'

    });
 
   
})
$('.product_gallery_item img').click(function(){
  var link=$(this).attr('src');
  var targetid = $(this).data("targetid");
  $('#ProductImg'+targetid).attr('src',link);
  $('a.zoom').attr('href',link);
  $('#ProductImg'+targetid).attr('data-zoom-image',link);
})
$("#ProductImg-detail").elevateZoom({
    cursor: "crosshair",
    easing : true, 
    gallery:'product_item_gallery-detail',
    // zoomType: "inner", 
    galleryActiveClass: "active"
  }); 
  $('#product_item_gallery-detail').slick({
      dots:false,
      arrows:true,
      fade:false,
      autoplay:false,
      slidesToShow: 5,
      slidesToScroll: 1,
       prevArrow: '<div class="slick-arrow slick-prev"><i class="flaticon-left-arrow"></i></div>',
            nextArrow: '<div class="slick-arrow slick-next"><i class="flaticon-next"></i></div>'

    });
    // $('.product_gallery_item img').click(function(){
    //   var link=$(this).attr('src');
    //   $('#ProductImg').attr('src',link);
    //   $('#ProductImg').attr('data-zoom-image',link);
    // })

function setMyCookie(viewmode) {
    myCookieVal = $('.demo_changer').hasClass('active') ? 'isActive' : 'notActive';
    $.cookie('myCookieName',viewmode, { path: '/' });  
    checkcookie();  
}
function checkcookie(){
 if ($.cookie('myCookieName') == 'gridView') {
  $('.viewmode').removeClass('listView');
      $('.viewmode').addClass('gridView');  
      $('.view-mode a').removeClass('active');
      $("#gridView").addClass('active');  
  } 
  if ($.cookie('myCookieName') == 'listView') {
      $('.viewmode').addClass('listView'); 
      $('.viewmode').removeClass('gridView');
      $('.view-mode a').removeClass('active');
      $("#listView").addClass('active'); 

  } 
}
 
$("#gridView").click(function (e) { 
   e.preventDefault();
    setMyCookie('gridView');
});
$("#listView").click(function (e) {  
    e.preventDefault();
    setMyCookie('listView');
});
checkcookie();

$('.makeoffer-dropdown a[data-toggle="modal"],.makeoffer-dropdown').click(function(e) {
    e.stopPropagation();
    $($(this).data('target')).modal('show', $(this));
});

// --------------input-width-automatic----------
// function changeWidth(){


// var getInputValueWidth = (function(){
//   function copyNodeStyle(sourceNode, targetNode) {
//     var computedStyle = window.getComputedStyle(sourceNode);
//     Array.from(computedStyle).forEach(key => targetNode.style.setProperty(key, computedStyle.getPropertyValue(key), computedStyle.getPropertyPriority(key)))
//   }
  
//   function createInputMeassureElm( inputelm ){
//     var meassureElm = document.createElement('span');
//     copyNodeStyle(inputelm, meassureElm);
//     meassureElm.style.width = 'auto';
//     meassureElm.style.position = 'absolute';
//     meassureElm.style.left = '-9999px';
//     meassureElm.style.top = '-9999px';
//     meassureElm.style.whiteSpace = 'pre';
    
//     meassureElm.textContent = inputelm.value || '';
//     document.body.appendChild(meassureElm);
//     return meassureElm;
//   }
//   return function(){
//     return createInputMeassureElm(this).offsetWidth;
//   }
// })();
// document.body.addEventListener('input', onInputDelegate)
// function onInputDelegate(e){
//   if( e.target.classList.contains('autoSize') )
//     e.target.style.width = getInputValueWidth.call(e.target) + 'px';
// }
// for( let input of document.querySelectorAll('input') )
//    onInputDelegate({target:input})
// }
// changeWidth();

$('.zoom-gallery').magnificPopup({
    delegate: '.zoom',
    type: 'image',
    closeOnContentClick: true,
    closeBtnInside: true,
    mainClass: 'mfp-with-zoom mfp-img-mobile',
    image: {
      verticalFit: true,
      // titleSrc: function(item) {
      //   return item.el.attr('title') + ' &middot; <a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank">image source</a>';
      // }
    },
    gallery: {
      enabled: true
    },
    zoom: {
      enabled: true,
      duration: 300, // don't foget to change the duration also in CSS
      opener: function(element) {
        return element.find('img');
      }
    }
    
  });


$(document).on('opening', '.remodal', function () {
  console.log('Modal is opening');
});