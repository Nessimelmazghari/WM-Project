document.addEventListener('DOMContentLoaded', function () {
    const autoAddCheckbox = document.getElementById('autoAddCheckbox');
    const addSeriesButton = document.getElementById('addSeriesButton');
  
    autoAddCheckbox.addEventListener('change', function () {
      addSeriesButton.style.display = this.checked ? 'inline-block' : 'none';
    });
  
    addSeriesButton.addEventListener('click', function () {
      const seriesData = {
        title: 'Stranger Adventures',
        description: 'A thrilling adventure that explores unknown realms and mysterious events.',
      };
      
      addSeriesToPlatform(seriesData);
    });
  });
  
  function addSeriesToPlatform(seriesData) {
    console.log('Auto-adding series:', seriesData);
    alert(`Series "${seriesData.title}" has been added to the platform!`);
    // Here you can integrate your API call logic if you have an endpoint to auto-add the series.
  }
  document.addEventListener('DOMContentLoaded', function () {
    const movieTitleDisplay = document.getElementById('movieTitleDisplay');
    
    // Retrieve the movie title from localStorage
    const movieTitle = localStorage.getItem('autoAddMovieTitle');
    
    if (movieTitle) {
      movieTitleDisplay.textContent = movieTitle;
    } else {
      movieTitleDisplay.textContent = 'No movie selected for auto-add.';
    }
  });
  