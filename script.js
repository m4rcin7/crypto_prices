function loadCryptoPrices() {
    console.log("Fetching crypto prices...");
  
    fetch("crypto_prices.php")
      .then(async (response) => {
        console.log("HTTP status:", response.status);
        const text = await response.text();
        console.log("Raw response:", text);
  
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }
        try {
          return JSON.parse(text);
        } catch (e) {
          throw new Error("Invalid JSON: " + e.message);
        }
      })
      .then((data) => {
        const pricesContainer = document.getElementById("cryptoPrices");
        pricesContainer.innerHTML = "";
  
        if (!Array.isArray(data)) {
          console.error("Invalid server response:", data);
          pricesContainer.innerHTML = "<p>Invalid data format</p>";
          return;
        }
  
        data.forEach((crypto) => {
          const card = document.createElement("div");
          card.className = "crypto-card";
  
          const changeClass =
            crypto.change.startsWith("-") ? "negative" : "positive";
  
          card.innerHTML = `
            <h2>${crypto.name} (${crypto.symbol})</h2>
            <p class="price">$${crypto.price}</p>
            <p class="change ${changeClass}">${crypto.change}</p>
          `;
          pricesContainer.appendChild(card);
        });
      })
      .catch((error) => {
        console.error("Error loading data:", error);
        document.getElementById("cryptoPrices").innerHTML =
          "<p>Error loading data</p>";
      });
  }
  
  setInterval(loadCryptoPrices, 20000);
  loadCryptoPrices();
  