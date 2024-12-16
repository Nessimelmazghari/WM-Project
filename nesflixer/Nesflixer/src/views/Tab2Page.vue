<template>
  <div>
    <button @click="startVoiceRecognition">Start Spraakherkenning</button>
    <p>{{ transcription }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      recognition: null,
      isRecognitionActive: false,
      transcription: "",
    };
  },
  methods: {
    startVoiceRecognition() {
      // Controleer of de Web Speech API beschikbaar is
      if (!("webkitSpeechRecognition" in window)) {
        alert("Web Speech API wordt niet ondersteund in deze browser.");
        return;
      }

      // Voorkom dat we meerdere keren starten zonder te stoppen
      if (this.isRecognitionActive) {
        console.log("Spraakherkenning is al actief.");
        return;
      }

      // Maak een nieuwe instance van webkitSpeechRecognition
      if (!this.recognition) {
        this.recognition = new webkitSpeechRecognition();
        this.recognition.lang = "nl-NL"; // Stel de taal in op Nederlands
        this.recognition.continuous = true; // Blijf luisteren totdat je stopt
        this.recognition.interimResults = true; // Zet de tussentijdse resultaten aan

        // Wanneer er resultaten zijn
        this.recognition.onresult = (event) => {
          const transcript = event.results[event.results.length - 1][0].transcript;
          console.log("Herkenning:", transcript);
          this.transcription = transcript; // Zet het transcript in de data
        };

        // Bij een fout in de spraakherkenning
        this.recognition.onerror = (event) => {
          console.error("Spraakherkenning fout:", event.error);
          if (event.error === "no-speech" || event.error === "aborted") {
            console.log("Herstarten na fout:", event.error);
            setTimeout(() => {
              this.startVoiceRecognition(); // Herstart spraakherkenning bij bepaalde fouten
            }, 1000);
          } else {
            this.isRecognitionActive = false; // Stop bij andere fouten
          }
        };

        // Wanneer de spraakherkenning wordt beëindigd
        this.recognition.onend = () => {
          console.log("Spraakherkenning beëindigd.");
          this.isRecognitionActive = false;

          // Log de status van de spraakherkenning
          if (!this.isRecognitionActive) {
            console.log("Spraakherkenning is gestopt, geen herstart.");
          }
        };
      }

      // Start de spraakherkenning
      this.isRecognitionActive = true;
      this.recognition.start();
      console.log("Spraakherkenning gestart.");
    },
  },
};
</script>

<style scoped>
/* Voeg hier je stijlen toe */
</style>
