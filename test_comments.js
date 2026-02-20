const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ args: ['--no-sandbox'] });
  const page = await browser.newPage();
  
  page.on('console', msg => console.log('PAGE LOG:', msg.text()));
  page.on('pageerror', error => console.log('PAGE ERROR:', error.message));
  page.on('response', response => {
      // console.log(response.status(), response.url());
  });

  await page.goto('http://localhost:8000/phones/4', {waitUntil: 'networkidle2'});
  
  // Try to submit comment
  await page.type('textarea[placeholder*="What do you think"]', 'Puppeteer test comment');
  await page.click('button[type="submit"]');
  
  await page.waitForTimeout(2000);
  
  await browser.close();
})();
