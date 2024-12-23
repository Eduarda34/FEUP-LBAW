function addEventListeners() {    
    let voteIcons = document.querySelectorAll('#post .post-footer .votes .vote-icon');
    [].forEach.call(voteIcons, function(icon) {
        icon.addEventListener('click', sendVoteRequest);
    });

    let commentVoteIcons = document.querySelectorAll('.comment-footer .votes .vote-icon');
    [].forEach.call(commentVoteIcons, function(icon) {
        icon.addEventListener('click', sendCommentVoteRequest);
    });

    let favoriteIcon = document.querySelector('#post .favorite-icon');
    if (favoriteIcon != null) {
        favoriteIcon.addEventListener('click', toggleFavorite);
    }

    let followBtn = document.querySelector('#posts #category-title #follow-btn');
    if (followBtn != null) {
        followBtn.addEventListener('click', toggleFollowCategory);
    }
}
  
  function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
  }
  
  function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();
  
    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.addEventListener('load', handler);
    request.send(encodeForAjax(data));
  }

/* %%%%%%%%%%%%%%%%%%%%%% VOTE IN POST REQUEST %%%%%%%%%%%%%%%%%%%%%% */
function sendVoteRequest(event) {
  event.preventDefault();

  let voteIcon = event.target;
  let postId = voteIcon.getAttribute('data-id');
  let isLike = voteIcon.getAttribute('data-is-like');

  sendAjaxRequest('POST', `/api/posts/${postId}/vote`, { is_like: isLike }, handleVoteResponse);
}

function handleVoteResponse() {
  if (this.status === 201 || this.status === 200) {
    // Parse the JSON response
    let response = JSON.parse(this.responseText);

    // Ensure the response contains the required data
    if (response.post_id) {
      // Select the vote containers for the specific post
      let voteContainers = document.querySelectorAll(`#post .post-footer .votes .vote-icon[data-id="${response.post_id}"]`);
      // Update vote counts and filled status for each vote icon
      voteContainers.forEach((voteIcon) => {
        let isLike = voteIcon.getAttribute('data-is-like') === "1"; // Check if it's the upvote or downvote icon
        let correspondingCountSpan = voteIcon.nextElementSibling; // The <span> next to the icon with the count
        if (correspondingCountSpan && response.vote_count) {
          // Update the vote count
          correspondingCountSpan.textContent = isLike ? response.vote_count.up : response.vote_count.down;
        }

        // Handle filled class
        if (this.status === 201) { // New vote
          if (response.is_like == isLike) {
            voteIcon.classList.add('filled');
          }
        } else if (this.status === 200) { // Update vote
          if (response.is_like == isLike) {
            voteIcon.classList.add('filled');
          } else if (response.is_like == !isLike) {
            voteIcon.classList.remove('filled');
          } else {
            voteIcon.classList.remove('filled');
          }
        }
      });
    }
  } else {
    console.error('Error voting:', this.status, this.responseText.error);
  }
}

/* %%%%%%%%%%%%%%%%%%%%%% VOTE IN COMMENT REQUEST %%%%%%%%%%%%%%%%%%%%%% */
function sendCommentVoteRequest(event) {
  event.preventDefault();

  let voteIcon = event.target;
  let commentId = voteIcon.getAttribute('data-id');
  let isLike = voteIcon.getAttribute('data-is-like');

  sendAjaxRequest('POST', `/api/comments/${commentId}/vote`, { is_like: isLike }, handleCommentVoteResponse);
}

function handleCommentVoteResponse() {
  if (this.status === 201 || this.status === 200) {
    // Parse the JSON response
    let response = JSON.parse(this.responseText);

    // Ensure the response contains the required data
    if (response.comment_id) {
      // Select the vote containers for the specific comment
      let voteContainers = document.querySelectorAll(`.comment-footer .votes .vote-icon[data-id="${response.comment_id}"]`);
      // Update vote counts and filled status for each vote icon
      voteContainers.forEach((voteIcon) => {
        let isLike = voteIcon.getAttribute('data-is-like') === "1"; // Check if it's the upvote or downvote icon
        let correspondingCountSpan = voteIcon.nextElementSibling; // The <span> next to the icon with the count
        if (correspondingCountSpan && response.vote_count) {
          // Update the vote count
          correspondingCountSpan.textContent = isLike ? response.vote_count.up : response.vote_count.down;
        }

        // Handle filled class
        if (this.status === 201) { // New vote
          if (response.is_like == isLike) {
            voteIcon.classList.add('filled');
          }
        } else if (this.status === 200) { // Update vote
          if (response.is_like == isLike) {
            voteIcon.classList.add('filled');
          } else if (response.is_like == !isLike) {
            voteIcon.classList.remove('filled');
          } else {
            voteIcon.classList.remove('filled');
          }
        }
      });
    }
  } else {
    console.error('Error voting:', this.status, this.responseText.error);
  }
}

/* %%%%%%%%%%%%%%%%%%%%%% ADD POST TO FAVORITES REQUEST %%%%%%%%%%%%%%%%%%%%%% */
function toggleFavorite(event) {
  event.preventDefault();

  let favoriteIcon = event.target;
  let postId = favoriteIcon.getAttribute('data-id');
  let isFavorite = favoriteIcon.classList.contains('filled');

  let url = `/api/posts/${postId}/favorites`;
  let method = isFavorite ? 'DELETE' : 'POST';

  sendAjaxRequest(method, url, {}, handleFavoriteResponse);
}

function handleFavoriteResponse() {
  if (this.status == 200 || this.status == 201) {
    let response = JSON.parse(this.responseText);
    let favoriteIcon = document.querySelector('#post .favorite-icon');
      
    favoriteIcon.classList.toggle('filled');
  } else {
    console.error('Failed to update favorite status:', this.status, this.responseText.error);
  }
}

/* %%%%%%%%%%%%%%%%%%%%%% FOLLOW/UNFOLLOW CATEGORY %%%%%%%%%%%%%%%%%%%%%% */
function toggleFollowCategory(event) {
  event.preventDefault();

  let followBtn = event.target;
  let categoryId = followBtn.getAttribute('data-id');
  let action = followBtn.classList.contains('inverted') ? 'DELETE' : 'POST';
  
  sendAjaxRequest(action, `/api/users/categories/${categoryId}`, {}, handleFollowCategoryResponse);
}

function handleFollowCategoryResponse() {
  if (this.status === 200 || this.status === 201) {
    // Parse the JSON response
    let response = JSON.parse(this.responseText);

    let followBtn = document.querySelector('#posts #category-title #follow-btn');

    // Toggle the class and the text on the button
    if (followBtn.classList.contains('inverted')) {
        followBtn.classList.remove('inverted');
        followBtn.textContent = 'Follow';
    } else {
        followBtn.classList.add('inverted');
        followBtn.textContent = 'Unfollow';
    }
  } else {
    console.error('Error following/unfollowing category:', this.status, this.responseText);
  }
}
  
addEventListeners();

document.addEventListener('DOMContentLoaded', function () {
  const imageInput = document.getElementById('profile_picture');
  const previewImage = document.querySelector('.profile-pic');

  if (imageInput) {
    imageInput.addEventListener('change', function(event) {
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = function(e) {
          previewImage.src = e.target.result;
      };

      if (file) {
          reader.readAsDataURL(file);
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const imageInput = document.getElementById('news-image');
  const previewImage = document.getElementById('preview-image');
  
  if (imageInput) {
    imageInput.addEventListener('change', function (event) {
      const file = event.target.files[0];
      const reader = new FileReader();
      
      reader.onload = function (e) {
          previewImage.src = e.target.result;
          previewImage.style.display = 'block';
      };

      if (file) {
          reader.readAsDataURL(file);
      }
    });
  }
});
    