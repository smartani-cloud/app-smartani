<<<<<<< HEAD

<script>
    function show1() {
    var page1 = document.getElementById("page1");
    var page2 = document.getElementById("page2");
        page1.style.display = "block";
        page2.style.display = "none";
    }
</script>
<script>
    function show2() {
    var page1 = document.getElementById("page1");
    var page2 = document.getElementById("page2");
    var page3 = document.getElementById("page3");
        page1.style.display = "none";
        page2.style.display = "block";
        page3.style.display = "none";
    }
</script>
<script>
    function show3() {
        var page2 = document.getElementById("page2");
        var page3 = document.getElementById("page3");
        var page4 = document.getElementById("page4");
        page2.style.display = "none";
        page3.style.display = "block";
        page4.style.display = "none";
    }
</script>
<script>
    function show4() {
        var page3 = document.getElementById("page3");
        var page4 = document.getElementById("page4");
        var page5 = document.getElementById("page5");
        page3.style.display = "none";
        page4.style.display = "block";
        page5.style.display = "none";
    }
</script>
<script>
    function show5() {
        var page4 = document.getElementById("page4");
        var page5 = document.getElementById("page5");
        var page6 = document.getElementById("page6");
        page4.style.display = "none";
        page5.style.display = "block";
        page6.style.display = "none";
    }
</script>
<script>
    function show6() {
        var page4 = document.getElementById("page4");
        var page5 = document.getElementById("page5");
        var page6 = document.getElementById("page6");
        page4.style.display = "none";
        page6.style.display = "block";
        page5.style.display = "none";
    }
</script>
<script>
    function showmodals() {
        var page5 = document.getElementById("page5");
        var page6 = document.getElementById("page6");
        page5.style.display = "none";
        page6.style.display = "block";
    }
</script>
<script>
    function checkBox() {
        // Get the checkbox
        var checkBox = document.getElementById("myCheck");
        // Get the output text
        var InputPegawai = document.getElementById("InputPegawai");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true){
            InputPegawai.style.display = "block";
        } else {
            InputPegawai.style.display = "none";
        }
    }
</script>
<script>
$(document).ready(function()
{

    jQuery('select[name="asal_sekolah"]').on('change',function(){
        var sekolah_lain = document.getElementById("sekolah_lain");
        var alamat_sekolah_lain = document.getElementById("alamat_sekolah_lain");
        if(sekolah_lain.selected){
            alamat_sekolah_lain.style.display = "block";
        }else{
            alamat_sekolah_lain.style.display = "none";
        }
    });
});
=======

<script>
    function show1() {
    var page1 = document.getElementById("page1");
    var page2 = document.getElementById("page2");
        page1.style.display = "block";
        page2.style.display = "none";
    }
</script>
<script>
    function show2() {
    var page1 = document.getElementById("page1");
    var page2 = document.getElementById("page2");
    var page3 = document.getElementById("page3");
        page1.style.display = "none";
        page2.style.display = "block";
        page3.style.display = "none";
    }
</script>
<script>
    function show3() {
        var page2 = document.getElementById("page2");
        var page3 = document.getElementById("page3");
        var page4 = document.getElementById("page4");
        page2.style.display = "none";
        page3.style.display = "block";
        page4.style.display = "none";
    }
</script>
<script>
    function show4() {
        var page3 = document.getElementById("page3");
        var page4 = document.getElementById("page4");
        var page5 = document.getElementById("page5");
        page3.style.display = "none";
        page4.style.display = "block";
        page5.style.display = "none";
    }
</script>
<script>
    function show5() {
        var page4 = document.getElementById("page4");
        var page5 = document.getElementById("page5");
        var page6 = document.getElementById("page6");
        page4.style.display = "none";
        page5.style.display = "block";
        page6.style.display = "none";
    }
</script>
<script>
    function show6() {
        var page4 = document.getElementById("page4");
        var page5 = document.getElementById("page5");
        var page6 = document.getElementById("page6");
        page4.style.display = "none";
        page6.style.display = "block";
        page5.style.display = "none";
    }
</script>
<script>
    function showmodals() {
        var page5 = document.getElementById("page5");
        var page6 = document.getElementById("page6");
        page5.style.display = "none";
        page6.style.display = "block";
    }
</script>
<script>
    function checkBox() {
        // Get the checkbox
        var checkBox = document.getElementById("myCheck");
        // Get the output text
        var InputPegawai = document.getElementById("InputPegawai");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true){
            InputPegawai.style.display = "block";
        } else {
            InputPegawai.style.display = "none";
        }
    }
</script>
<script>
$(document).ready(function()
{

    jQuery('select[name="asal_sekolah"]').on('change',function(){
        var sekolah_lain = document.getElementById("sekolah_lain");
        var alamat_sekolah_lain = document.getElementById("alamat_sekolah_lain");
        if(sekolah_lain.selected){
            alamat_sekolah_lain.style.display = "block";
        }else{
            alamat_sekolah_lain.style.display = "none";
        }
    });
});
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>