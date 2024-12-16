<template>
  <ion-header>
    <ion-toolbar>
      <ion-title>Online Video Sync met Spraakbediening</ion-title>
    </ion-toolbar>
  </ion-header>

  <ion-content>
    <!-- Zoekopdracht invoeren -->
    <ion-item>
      <ion-label position="floating">Zoek naar video's</ion-label>
      <ion-input
        :value="searchQuery"
        placeholder="Zoek naar video's..."
        @input="handleInput"
      ></ion-input>
    </ion-item>

    <!-- Zoekknop -->
    <ion-button expand="full" @click="searchVideos">Zoek Video's</ion-button>

    <!-- Zoekresultaten tonen -->
    <div id="searchResults">
      <div v-for="(video, index) in videos" :key="index">
        <ion-item button @click="playVideo(video)">
          <ion-label>{{ video.title }}</ion-label>
        </ion-item>
      </div>
    </div>

    <!-- Video container voor het afspelen van geselecteerde video's -->
    <div id="videoContainer"></div>

    <!-- Spraakherkenning knop -->
    <ion-button expand="full" @click="startVoiceRecognition">Start Spraakbediening</ion-button>
  </ion-content>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: "", // De zoekopdracht van de gebruiker
      videos: [], // De zoekresultaten
      currentVideoElement: null, // Het huidige video-element dat wordt afgespeeld
      recognition: null, // De instantie van de spraakherkenning
      isRecognitionActive: false, // Status voor het controleren van spraakherkenning
    };
  },
  methods: {
    // Methode voor het bijhouden van de invoer in het zoekveld
    handleInput(event) {
      this.searchQuery = event.target.value; // Update de zoekopdracht
      console.log("Huidige zoekopdracht:", this.searchQuery); // Log de zoekopdracht
    },

    // Methode voor het zoeken van video's
    async searchVideos() {
      console.log("Zoekopdracht:", this.searchQuery);

      // Controleer of de zoekopdracht niet leeg is
      if (this.searchQuery.trim()) {
        try {
          const response = await fetch(
            `https://nessimelmazghari-odisee.be/nesflixer/video_search_api.php?query=${encodeURIComponent(
              this.searchQuery
            )}`
          );

          const data = await response.json();
          console.log("API Response:", data);

          // Als er resultaten zijn, worden ze opgeslagen in de videos array
          if (data && data.results) {
            this.videos = data.results;
          } else {
            console.error("Geen resultaten gevonden.");
            alert("Geen resultaten gevonden voor je zoekopdracht.");
          }
        } catch (error) {
          console.error("Fout bij het ophalen van video's:", error);
          alert("Er is een fout opgetreden bij het ophalen van video's.");
        }
      } else {
        console.log("Zoekopdracht is leeg.");
        alert("Voer een zoekopdracht in.");
      }
    },

    // Methode om de geselecteerde video af te spelen
    playVideo(video) {
      const videoContainer = document.getElementById("videoContainer");
      videoContainer.innerHTML = ""; // Verwijder de vorige video

      let newVideoElement;

      // Controleer of de video een YouTube-video is
      if (video.url.includes("youtube")) {
        const iframe = document.createElement("iframe");
        iframe.src = `https://www.youtube.com/embed/${this.getYouTubeVideoId(
          video.url
        )}`;
        iframe.allow =
          "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
        iframe.id = "youtubeIframe";
        videoContainer.appendChild(iframe);
        this.currentVideoElement = iframe;
      } else {
        newVideoElement = document.createElement("video");
        newVideoElement.controls = true;
        const videoSource = document.createElement("source");
        videoSource.src = video.url;
        videoSource.type = "video/mp4"; // Stel het juiste type in
        newVideoElement.appendChild(videoSource);
        videoContainer.appendChild(newVideoElement);
        newVideoElement.load();
        newVideoElement.play();
        this.currentVideoElement = newVideoElement;
      }

      // Start spraakherkenning pas nadat de video is geladen en gestart
      newVideoElement?.addEventListener("playing", () => {
        // Start spraakherkenning als de video start met afspelen
        this.startVoiceRecognition();
      });
    },

    // Methode om de YouTube video-id uit een URL te extraheren
    getYouTubeVideoId(url) {
      const regex =
        /(?:https?:\/\/(?:www\.)?youtube\.com\/(?:[^/]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*\?v=|(?:\/|%2F)\/))([^"&?/\s]{11})/;
      const match = url.match(regex);
      return match ? match[1] : null;
    },

    // Methode voor spraakherkenning
    startVoiceRecognition() {
      // Controleer of de Web Speech API beschikbaar is
      if (!("webkitSpeechRecognition" in window)) {
        alert("Web Speech API wordt niet ondersteund in deze browser.");
        return;
      }

      // Controleer eerst of er een microfoon beschikbaar is
      navigator.mediaDevices.enumerateDevices()
        .then(devices => {
          const audioDevices = devices.filter(device => device.kind === "audioinput");
          if (audioDevices.length === 0) {
            throw new Error("Geen microfoon gevonden.");
          } else {
            // Vraag expliciet om toegang tot de microfoon
            navigator.mediaDevices.getUserMedia({ audio: true })
              .then(() => {
                console.log("Microfoon toegang is toegestaan.");

                // Controleer of spraakherkenning al actief is
                if (this.isRecognitionActive) {
                  console.log("Spraakherkenning is al actief.");
                  return;
                }

                // Maak een nieuwe instance van webkitSpeechRecognition als deze nog niet bestaat
                if (!this.recognition) {
                  this.recognition = new webkitSpeechRecognition();
                  this.recognition.lang = "nl-NL";
                  this.recognition.continuous = true;
                  this.recognition.interimResults = true; // Zet interimResults op true voor tussentijdse resultaten

                  this.recognition.onresult = (event) => {
                    const command = event.results[event.results.length - 1][0].transcript.toLowerCase();
                    console.log("Herkenning: ", command);

                    if (command.includes("afspelen")) {
                      this.currentVideoElement.play();
                    } else if (command.includes("pauze")) {
                      this.currentVideoElement.pause();
                    } else if (command.includes("terugspoelen")) {
                      if (this.currentVideoElement.tagName === "VIDEO") {
                        this.currentVideoElement.currentTime -= 10;
                      }
                    } else if (command.includes("vooruitspoelen")) {
                      if (this.currentVideoElement.tagName === "VIDEO") {
                        this.currentVideoElement.currentTime += 10;
                      }
                    }
                  };

                  this.recognition.onend = () => {
                    console.log("Spraakherkenning beëindigd.");
                    // Start opnieuw als de spraakherkenning stopt, maar alleen als de flag isRecognitionActive nog niet false is
                    if (this.isRecognitionActive) {
                      this.recognition.start();
                      console.log("Herstarten van spraakherkenning...");
                    }
                  };

                  this.recognition.onerror = (event) => {
                    console.error("Spraakherkenning fout:", event.error);
                    alert("Spraakherkenning fout: " + event.error);
                    this.isRecognitionActive = false; // Zorg ervoor dat de status correct wordt bijgewerkt
                  };
                }

                // Start de spraakherkenning
                this.isRecognitionActive = true;
                this.recognition.start();
              })
              .catch((err) => {
                console.error("Toegang tot de microfoon geweigerd:", err);
                alert("Toegang tot de microfoon is geweigerd. Zorg ervoor dat je toestemming hebt gegeven voor microfoon toegang.");
              });
          }
        })
        .catch((err) => {
          console.error("Fout bij het ophalen van microfoons:", err);
          alert("Geen microfoon gevonden. Zorg ervoor dat er een microfoon is aangesloten.");
        });
    }
  }
};
</script>

<style scoped>
#videoContainer {
  margin-top: 20px;
  width: 100%;
  height: auto;
}

ion-item {
  margin-top: 10px;
}

ion-input {
  margin: 20px;
}

ion-button {
  margin: 20px;
}
</style>
