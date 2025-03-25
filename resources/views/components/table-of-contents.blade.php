<div id="floating-toc">
  <button id="toc-toggle">Mục lục</button> 
  <div class="toc-content">
    <div id="table-of-contents"></div> 
  </div>
</div>

<script>
  // Function to generate the table of contents
  function generateTableOfContents() {
    const content = document.getElementById('post-content');
    const headings = content.querySelectorAll('h2, h3, h4, h5, h6');
    const toc = document.getElementById('table-of-contents');
    const tocList = document.createElement('ul');

    headings.forEach(heading => {
      const title = heading.textContent;
      heading.id = title.replace(/ /g, '-'); // Ensure the heading has an ID
      const link = '#' + heading.id;

      const listItem = document.createElement('li');
      const linkElement = document.createElement('a');
      linkElement.href = link;
      linkElement.textContent = title;

      listItem.appendChild(linkElement);
      tocList.appendChild(listItem);
    });

    toc.appendChild(tocList);
  }

  // Call the function when the page loads
  window.addEventListener('DOMContentLoaded', generateTableOfContents);


  // JavaScript for the floating table of contents
  window.addEventListener('scroll', function() {
    const floatingTOC = document.getElementById('floating-toc');
    if (window.scrollY > 200) { 
      floatingTOC.style.display = 'block';
    } else {
      floatingTOC.style.display = 'none';
    }
  }); 

  // JavaScript for the toggle button
  const tocToggle = document.getElementById('toc-toggle');
  const floatingTOC = document.getElementById('floating-toc');

  tocToggle.addEventListener('click', function() {
    floatingTOC.classList.toggle('hidden'); 
  });
</script>