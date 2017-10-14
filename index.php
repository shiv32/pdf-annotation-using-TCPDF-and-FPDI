<!DOCTYPE html>
<html>

<head>
    <!--
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="//mozilla.github.io/pdf.js/build/pdf.js"></script>
    -->
<script src="jquery.js"></script>
<script src="pdf.js"></script>
<style>
#the-canvas {
  border:1px solid black;
}
</style>
</head>

<body>
<h1>PDF.js Previous/Next example</h1>

<div>
  <button id="prev">Previous</button>
  <button id="next">Next</button>
  &nbsp; &nbsp;
  <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>
<input type="text" id="posd" placeholder="mouse down"/>
<input type="text" id="posu" placeholder="mouse up"/>
</div>

<canvas id="the-canvas"></canvas>

<script>
// If absolute URL from the remote server is provided, configure the CORS
// header on that server.
var url = 'test.pdf';

// The workerSrc property shall be specified.
//PDFJS.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';
PDFJS.workerSrc = 'pdf.worker.js';

var pdfDoc = null,
    pageNum = 1,
    pageRendering = false,
    pageNumPending = null,
   // scale = 0.8,
         scale = 1.5,
    //scale = 0.352778,
    canvas = document.getElementById('the-canvas'),
    ctx = canvas.getContext('2d');

/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
  pageRendering = true;
  // Using promise to fetch the page
  pdfDoc.getPage(num).then(function(page) {
    var viewport = page.getViewport(scale);
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Render PDF page into canvas context
    var renderContext = {
      canvasContext: ctx,
      viewport: viewport
    };
    var renderTask = page.render(renderContext);

    // Wait for rendering to finish
    renderTask.promise.then(function() {
      pageRendering = false;
      if (pageNumPending !== null) {
        // New page rendering is pending
        renderPage(pageNumPending);
        pageNumPending = null;
      }
    });
  });

  // Update page counters
  document.getElementById('page_num').textContent = pageNum;
}

/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
  if (pageRendering) {
    pageNumPending = num;
  } else {
    renderPage(num);
  }
}

/**
 * Displays previous page.
 */
function onPrevPage() {
  if (pageNum <= 1) {
    return;
  }
  pageNum--;
  queueRenderPage(pageNum);
}
document.getElementById('prev').addEventListener('click', onPrevPage);

/**
 * Displays next page.
 */
function onNextPage() {
  if (pageNum >= pdfDoc.numPages) {
    return;
  }
  pageNum++;
  queueRenderPage(pageNum);
}
document.getElementById('next').addEventListener('click', onNextPage);

/**
 * Asynchronously downloads PDF.
 */
PDFJS.getDocument(url).then(function(pdfDoc_) {
  pdfDoc = pdfDoc_;
  document.getElementById('page_count').textContent = pdfDoc.numPages;

  // Initial/first page rendering
  renderPage(pageNum);
});



//for draw
var canvas, context, startX, endX, startY, endY;
var mouseIsDown = 0;

function init() {
    canvas = document.getElementById("the-canvas");
    context = canvas.getContext("2d");

    canvas.addEventListener("mousedown", mouseDown, false);
    canvas.addEventListener("mousemove", mouseXY, false);
    canvas.addEventListener("mouseup", mouseUp, false);
}

function mouseUp(eve) {
    if (mouseIsDown !== 0) {
        mouseIsDown = 0;
        var pos = getMousePos(canvas, eve);
        endX = pos.x;
        endY = pos.y;
        drawSquare(); //update on mouse-up
    }
}

function mouseDown(eve) {
    mouseIsDown = 1;
    var pos = getMousePos(canvas, eve);
    startX = endX = pos.x;
    startY = endY = pos.y;
    drawSquare(); //update
}

function mouseXY(eve) {

    if (mouseIsDown !== 0) {
        var pos = getMousePos(canvas, eve);
        endX = pos.x;
        endY = pos.y;

        drawSquare();
    }
}

function drawSquare() {
    // creating a square
    var w = endX - startX;
    var h = endY - startY;
    var offsetX = (w < 0) ? w : 0;
    var offsetY = (h < 0) ? h : 0;
    var width = Math.abs(w);
    var height = Math.abs(h);

    //context.clearRect(0, 0, canvas.width, canvas.height);
   
    context.beginPath();
    context.rect(startX + offsetX, startY + offsetY, width, height);
    context.fillStyle = "yellow";
   //context.fillStyle = 'rgba(225,225,0,0.5)';
      context.globalAlpha = 0.2;
      context.fill();
     context.globalAlpha = 1.0;
    //context.lineWidth = 7;
    //context.strokeStyle = 'black';
    context.stroke();
 
}

function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}
init();

$("#the-canvas").mouseup(function(event) {
   $("#posu").val( "up: (" +event.pageX +","+ event.pageY+")" );

  })
 $("#the-canvas").mousedown(function(event) {
  $("#posd").val( "dn: (" +event.pageX +","+ event.pageY+")" );
  });
var x1;
var y1;
var x2;
var y2;

 $("#the-canvas").mousedown(function(event) {
 var posX = $(this).position().left, posY = $(this).position().top;
 x1 = event.pageX - posX;
 y1 = event.pageY - posY;


$(this).mouseup(function(event) {
var posX = $(this).position().left, posY = $(this).position().top;
 x2 = event.pageX - posX;
 y2 = event.pageY - posY;
$.ajax({
        url: "anote.php",
        type: "post",
        data: {
               X1: x1,
               Y1: y1,
               X2: x2,
               Y2: y2,
           pageNo: pageNum,   
              },
        success: function (response) {
         alert('ok');  
         alert(response);
          location.reload();
         //window.location = 'anote.php';
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }


    });
});
});
</script>
</body>


</html>
