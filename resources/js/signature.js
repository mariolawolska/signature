

$(function () {

    /**
     * Add Signature
     */
    var wrapper = document.getElementById("signature-pad");
    var clearButton = wrapper.querySelector("[data-action=clear]");
    var savePNGButton = wrapper.querySelector("[data-action=save-png]");
    var canvas = wrapper.querySelector("canvas");
    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });
    window.onresize = resizeCanvasSignature;
    resizeCanvasSignature(canvas, signaturePad, window);
    clearButton.addEventListener("click", function (event) {
        signaturePad.clear();
    });
    savePNGButton.addEventListener("click", function (event) {
        if (signaturePad.isEmpty()) {
            alert("Please provide a signature first.");
        } else {
            var dataURL = signaturePad.toDataURL();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "PUT",
                url: "/public/saveSignature",
                data: {
                    dataURL: dataURL,
                    userId: $('#signature-pad').attr('userId')
                },
                beforeSend: function () {
                },
                success: function () {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
    });
});
/**
 * Add Signature END
 */

function resizeCanvasSignature(canvas, signaturePad, window) {
    if (window.devicePixelRatio != undefined) {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePad.clear();
    }
}

function download(dataURL, filename) {
    var blob = dataURLToBlob(dataURL);
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.style = "display: none";
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
}

function dataURLToBlob(dataURL) {
    var parts = dataURL.split(';base64,');
    var contentType = parts[0].split(":")[1];
    var raw = window.atob(parts[1]);
    var rawLength = raw.length;
    var uInt8Array = new Uint8Array(rawLength);
    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }
    return new Blob([uInt8Array], {type: contentType});
}