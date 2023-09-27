/** 
 * SOPRANO library
 */
// On document ready...
document.addEventListener("DOMContentLoaded", () => {
  getPlaylist();
});

const audio = document.getElementById("audio");
const player_cover = document.getElementById("player-cover");
let audio_hash = "";

let playlist = [];
let playlist_index = 0;

let current_track = {
  artist: "No artist",
  album: "No album",
  title: "No album",
  cover: "/assets/img/no-album.png",
};

/** Media Session */
const updateMetadata = (track) => {
  player_cover.src = current_track.cover;
  navigator.mediaSession.metadata = new MediaMetadata({
    title: current_track.title,
    artist: current_track.artist,
    album: current_track.album,
    artwork: [
      { src: current_track.cover, sizes: '96x96',   type: 'image/png' },
      { src: current_track.cover, sizes: '128x128', type: 'image/png' },
      { src: current_track.cover, sizes: '192x192', type: 'image/png' },
      { src: current_track.cover, sizes: '256x256', type: 'image/png' },
      { src: current_track.cover, sizes: '384x384', type: 'image/png' },
      { src: current_track.cover, sizes: '512x512', type: 'image/png' },
    ]
  });

  // Media is loaded, set the duration.
  updatePositionState();
}

const updatePositionState = () => {
  if ('setPositionState' in navigator.mediaSession) {
    navigator.mediaSession.setPositionState({
      duration: audio.duration,
      playbackRate: audio.playbackRate,
      position: audio.currentTime
    });
  }
}

navigator.mediaSession.setActionHandler('previoustrack', () => {
  prevTrack();
});

navigator.mediaSession.setActionHandler('nexttrack', () => {
  nextTrack();
});

navigator.mediaSession.setActionHandler('seekbackward', (event) => {
  seekBackward(event);
});

navigator.mediaSession.setActionHandler('seekforward', (event) => {
  seekForward(event);
});

navigator.mediaSession.setActionHandler('play', async () => {
  playTrack();
});

navigator.mediaSession.setActionHandler('pause', () => {
  pauseTrack();
});

try {
  navigator.mediaSession.setActionHandler('stop', () => {
    // TODO: Clear UI playback...
    pauseTrack();
  });
} catch(error) {
}

try {
  navigator.mediaSession.setActionHandler('seekto', (event) => {
    if (event.fastSeek && ('fastSeek' in audio)) {
      audio.fastSeek(event.seekTime);
      return;
    }
    audio.currentTime = event.seekTime;
    updatePositionState();
  });
} catch(error) {
}



/** Audio event listeners */
audio.addEventListener('ended', () => {
  nextTrack();
});

audio.addEventListener('play', () => {
  navigator.mediaSession.playbackState = 'playing';
});

audio.addEventListener('pause', () => {
  navigator.mediaSession.playbackState = 'paused';
});

/** Functions */
// Clear search input
const clearSearch = () => {
  document.getElementById("search-input").value = "";
}

// Set the audio src
const setTrack = (hash) => {
  if (audio_hash != hash) {
    audio_hash = hash;
    let new_src = `/music/play/${audio_hash}`;
    audio.src = new_src;
    trackInfo();
    playTrack();
  }
}

const playResult = (hash) => {
  setTrack(hash);
}

const playPlaylist = (hash, index) => {
  playlist_index = index;
  setTrack(hash);
}

// Grab the track info
const trackInfo = async () => {
  const res = await fetch(`/music/info/${audio_hash}`);
  const data = await res.json();
  current_track = data;
}

const getPlaylist = async () => {
  const res = await fetch(`/playlist/get`);
  const data = await res.json();
  playlist = data;
}

const addToPlaylist = async (hash) => {
  const res = await fetch(`/playlist/add/${hash}`);
  const data = await res.json();
  playlist = data;
}

const removeFromPlaylist = async (hash) => {
  const res = await fetch(`/playlist/remove/${hash}`);
  const data = await res.json();
  playlist = data;
  document.getElementById(hash).remove();
}

// Previous track
const prevTrack = () => {
  if (playlist.length === 1) return;
  playlist_index = (playlist_index - 1 + playlist.length) % playlist.length;
  let track = playlist[playlist_index];
  setTrack(track);
}

let defaultSkipTime = 10; /* Time to skip in seconds by default */

// Seek forward
const seekForward = (event) => {
  const skipTime = event.seekOffset || defaultSkipTime;
  audio.currentTime = Math.min(audio.currentTime + skipTime, audio.duration);
  updatePositionState();
}

// Seek backward
const seekBackward = (event) => {
  const skipTime = event.seekOffset || defaultSkipTime;
  audio.currentTime = Math.max(audio.currentTime - skipTime, 0);
  updatePositionState();
}

// Play track
const playTrack = () => {
  if (!audio.src && playlist.length > 0) {
    const track = playlist[0];
    setTrack(track);
  }
  audio.play()
  .then(_ => updateMetadata())
  .catch(err => console.log)
}

// Pause track
const pauseTrack = () => {
  audio.pause();
}

// Next track
const nextTrack = () => {
  if (playlist.length === 1) return;
  playlist_index = (playlist_index + 1) % playlist.length;
  let track = playlist[playlist_index];
  setTrack(track);
}
