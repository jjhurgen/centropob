function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
    } else {
      alert("Geolocalización no es compatible con este navegador.");
    }
  }

  function showPosition(position) {
    document.getElementById("longitud").value = position.coords.longitude;
    document.getElementById("latitud").value = position.coords.latitude;
  }