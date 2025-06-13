# Visualwise-Jules User Guide

**Subtitle:** Marketing Strategy Visualizer
**Version:** 1.0.0

---

## Table of Contents (Conceptual)

1.  Introduction
2.  Installation
3.  Admin Panel Configuration
4.  Using the Frontend Visualizer
5.  Understanding the Calculations
6.  Troubleshooting & FAQ
7.  Support & Contact

---

## Chapter 1: Introduction

### What is Visualwise-Jules?

Visualwise-Jules is a powerful WordPress plugin designed to help you project and visualize potential outcomes of your marketing strategies. By inputting various parameters for channels like Google Ads, Meta Ads (Facebook/Instagram), and SEO, you can see projected traffic, leads, and sales over a selected timespan. The plugin features a dynamic frontend interface with an interactive line graph, allowing for easy comparison and strategy planning.

### Key Features

*   **Multi-Channel Projections:** Supports Google Ads, Meta Ads, and SEO modules.
*   **Customizable Parameters:** Fine-tune calculations with specific factors, base amounts, and progression arrays via the WordPress admin panel.
*   **Dynamic Frontend Visualizer:** Interactive interface to add marketing modules, adjust budgets and timespans, and see results in real-time.
*   **Line Graph Visualization:** Clearly see projected trends for total sales, traffic, and leads. Toggle metrics on/off.
*   **Combined Summary:** Get an overview of total projected outcomes across all active modules.
*   **Currency Customization:** Set your preferred currency symbol, position, and spacing.
*   **Shortcode Integration:** Easily embed the visualizer on any page or post.

---

## Chapter 2: Installation

Installing Visualwise-Jules is straightforward, following standard WordPress plugin installation procedures:

1.  **Download:** If you have a `.zip` file of the plugin, download it to your computer.
2.  **Navigate to WordPress Admin:** Log in to your WordPress dashboard.
3.  **Go to Plugins:** In the left-hand menu, click on "Plugins" > "Add New".
4.  **Upload Plugin:**
    *   If you have a `.zip` file, click the "Upload Plugin" button at the top of the page.
    *   Click "Choose File", select the `visualwise-jules.zip` file from your computer, and click "Install Now".
5.  **Activate Plugin:** Once the installation is complete, click the "Activate Plugin" button.

Alternatively, if the plugin is available in the WordPress.org repository, you can search for "Visualwise-Jules" under "Plugins" > "Add New" and install and activate it directly.

`[Screenshot: WordPress Admin - Plugins > Add New page]`

---

## Chapter 3: Admin Panel Configuration

After installation, you'll find a new menu item "Visualwise-Jules" in your WordPress admin sidebar. Clicking this will take you to the plugin's settings page.

`[Screenshot: Visualwise-Jules Admin Menu in WordPress sidebar]`

The admin panel is organized into several tabs for easy configuration:

### 3.1 General Settings

This tab allows you to configure how currency values are displayed throughout the visualizer.

`[Screenshot: General Settings Tab in Admin Panel]`

*   **Currency Symbol:** Enter the symbol for your currency (e.g., $, £, €).
    *   *Default:* `$`
*   **Currency Position:** Choose where the currency symbol appears:
    *   `Prefix`: Before the numerical amount (e.g., $100.00).
    *   `Postfix`: After the numerical amount (e.g., 100.00$).
    *   *Default:* Prefix
*   **Currency Spacing:** Check this box to add a single space between the currency symbol and the amount (e.g., $ 100.00 instead of $100.00).
    *   *Default:* Unchecked (no space)

### 3.2 Google Ads Settings

Configure the parameters used for calculating Google Ads projections.

`[Screenshot: Google Ads Settings Tab in Admin Panel]`

*   **Sales Factor:** A multiplier applied to the calculated monthly Google Ads budget to estimate sales from this channel. *Example: If your monthly budget is 1000 and this factor is 0.5, the sales component from this factor would be 500.*
*   **Base Sales Amount:** A fixed sales amount added to the monthly calculation, regardless of the budget factor. This can represent a baseline performance. *Example: If set to 200, this amount is added to the sales calculated by the Sales Factor.*
*   **Traffic Factor:** A multiplier applied to the monthly budget to estimate website traffic. *Example: If your monthly budget is 1000 and this factor is 2, estimated traffic is 2000 visitors.*
*   **Leads Factor:** A multiplier applied to the monthly budget to estimate leads. *Example: If your monthly budget is 1000 and this factor is 0.1, estimated leads are 100.*
*   **Month 2+ Increase Factor (%):** A percentage value representing the expected monthly growth or improvement in performance for Google Ads, starting from the second month. This increase is applied *compounding* each month to the sales, traffic, and leads figures. *Example: If set to 5%, Month 2 projections will be 5% higher than Month 1. Month 3 will be 5% higher than the increased Month 2 figures, and so on.*

### 3.3 SEO Settings

Configure parameters for organic search (SEO) projections. SEO projections are typically based on consistent monthly investment and gradual improvements.

`[Screenshot: SEO Settings Tab in Admin Panel]`

*   **Base Sales Progression:** Enter a comma-separated list of numbers representing the expected monthly sales if your SEO monthly budget were **1000 currency units** (e.g., "$", "£", etc., matching your General Settings). *Example: `100,120,150,180,220,250` for 6 months.* The plugin will scale these values based on the actual "Monthly Budget" you set for the SEO module on the frontend visualizer.
*   **Base Traffic Progression:** Similar to sales, enter a comma-separated list for expected monthly website traffic for a 1000 unit budget. *Example: `1000,1200,1500,1900,2300,2800`.*
*   **Base Leads Progression:** Similar to sales, enter a comma-separated list for expected monthly leads for a 1000 unit budget. *Example: `10,12,15,18,22,25`.*

    **Important Note on Progression Arrays:**
    *   The values should reflect the expected outcome for each month at a consistent 1000 unit/month budget.
    *   If the "Timespan" selected on the frontend visualizer is longer than the number of entries in your progression arrays, the **last value in the array will be used** for all subsequent months.

### 3.4 Meta Ads Settings

Configure parameters for Meta Ads (Facebook/Instagram) projections. The logic is similar to Google Ads.

`[Screenshot: Meta Ads Settings Tab in Admin Panel]`

*   **Sales Factor:** Multiplier for monthly Meta Ads budget to estimate sales.
*   **Base Sales Amount:** Fixed sales amount added monthly.
*   **Traffic Factor:** Multiplier for monthly budget to estimate traffic.
*   **Leads Factor:** Multiplier for monthly budget to estimate leads.
*   **Month 2+ Increase Factor (%):** Compounding percentage increase applied monthly (from month 2 onwards) to sales, traffic, and leads for Meta Ads.

---

## Chapter 4: Using the Frontend Visualizer

Once configured, you can add the Visualwise-Jules visualizer to any WordPress page or post.

### 4.1 Adding the Shortcode

1.  Edit the page or post where you want the visualizer to appear.
2.  In the content editor (Gutenberg block editor or Classic editor), add the following shortcode:
    `[visualwise_jules_visualizer]`
3.  Save or update the page/post.

### 4.2 The Visualizer Interface

`[Screenshot: Frontend Visualizer - Initial Empty State]`

The interface consists of:
*   **Module Selection Area:** Buttons to add different marketing modules.
*   **Controls Panel:** Where active module settings (budget, timespan) are adjusted. This area is scrollable if many modules are added.
*   **Chart Area:** Displays the projection line graph. This area is also scrollable.
*   **Summary Section:** Shows combined total projections.

### 4.3 Step-by-Step Guide

1.  **Add a Marketing Module:**
    *   Click on a button in the "Add Projection Module" area (e.g., "Add Google Ads").
    *   A new card for that module will appear in the "Controls Panel".
    `[Screenshot: Frontend Visualizer - Adding a Google Ads Module]`

2.  **Adjust Budget and Timespan:**
    *   **Budget Slider:**
        *   For **Google Ads** and **Meta Ads**, this slider represents the **Daily Budget**.
        *   For **SEO**, this slider represents the **Monthly Budget**.
        *   The current value is displayed next to the slider label.
    *   **Timespan Slider:**
        *   Select the duration for the projection in months (1 to 24 months).
        *   The current value is displayed.
    `[Screenshot: Frontend Visualizer - Adjusting Budget and Timespan Sliders on a Module]`

3.  **View Module Projections:**
    *   As you adjust the sliders, the "Projected Traffic", "Projected Leads", and "Projected Sales" figures on the module card will update in real-time, showing the total for that specific module over the selected timespan.

4.  **Interpreting the Line Graph:**
    *   The line graph displays the **combined total projections** from all active modules.
    *   The X-axis shows months, and the Y-axis shows the values for sales, traffic, or leads.
    *   **Toggle Metrics:** Above the graph, you'll find checkboxes ("Sales", "Traffic", "Leads"). Uncheck a box to hide the corresponding line from the graph, allowing you to focus on specific metrics.
    `[Screenshot: Frontend Visualizer - Graph showing Sales and Traffic lines]`

5.  **Reading the Summary Totals:**
    *   Below the graph, the "Combined Projections Summary" section displays the grand totals for traffic, leads, and sales, summed up across all active modules and their entire projected timespans.
    `[Screenshot: Frontend Visualizer - Summary Section]`

6.  **Removing a Module:**
    *   Click the "X" button in the top-right corner of a module card to remove it from the controls panel and the calculation. The graph and summary will update accordingly.

---

## Chapter 5: Understanding the Calculations (Brief Overview)

### Google Ads / Meta Ads Modules:

*   **Monthly Budget:** Calculated as `Daily Budget * 30.44` (average days in a month).
*   **Base Monthly Projections:**
    *   Traffic: `Monthly Budget * Traffic Factor`
    *   Leads: `Monthly Budget * Leads Factor`
    *   Sales: `(Monthly Budget * Sales Factor) + Base Sales Amount`
*   **Month 2+ Growth:** The "Month 2+ Increase Factor" is applied compoundingly. For example, if the factor is 5%:
    *   Month 1 uses base projections.
    *   Month 2 = Month 1 projection * (1 + 0.05)
    *   Month 3 = Month 2 projection * (1 + 0.05)
    *   ...and so on for traffic, leads, and sales.

### SEO Module:

*   **Base Progressions:** The comma-separated values you enter in admin settings are for a standard 1000 currency unit monthly budget.
*   **Budget Scaling:** If your actual "Monthly Budget" on the frontend is different from 1000, the progression values are scaled proportionally.
    *   `Scale Factor = Actual Monthly Budget / 1000`
    *   `Projected Value for Month X = Base Progression Value for Month X * Scale Factor`
*   **Timespan Handling:** If the selected timespan is longer than your defined progression array, the last value in the array is repeated for subsequent months.

---

## Chapter 6: Troubleshooting & FAQ

*   **Q: The graph is not updating when I change slider values.**
    *   A: Ensure JavaScript is enabled in your browser. Check the browser's developer console (usually F12 key) for any error messages that might indicate a problem. Try refreshing the page.

*   **Q: My projected numbers seem too high/low.**
    *   A: Carefully review the factors and base amounts you've set in the admin panel for each relevant module. Small changes in factors can significantly impact projections. For SEO, remember the base progression values are for a 1000 unit budget.

*   **Q: The currency symbol or format is incorrect.**
    *   A: Check your settings under the "General" tab in the Visualwise-Jules admin panel.

*   **Q: Can I add more than one of the same module type (e.g., two Google Ads campaigns)?**
    *   A: The current version adds one instance per module type (Google Ads, SEO, Meta Ads). Each added module represents that entire channel.

*   **Q: Why does the SEO module use a monthly budget slider while Ads modules use daily?**
    *   A: This is a common way to think about these channels. SEO efforts are typically planned with a consistent monthly retainer or budget, while ad spend can be controlled and reported daily.

---

## Chapter 7: Support & Contact

If you encounter any issues or have questions not covered in this guide, please consider the following (examples, replace with actual links if they exist):

*   **Plugin Documentation:** Check the "Help/Documentation" tab within the plugin's admin settings for quick reference.
*   **Support Forum:** Visit our support forum at [https://example.com/visualwise-jules-support](https://example.com/visualwise-jules-support).
*   **Contact Us:** Reach out via our website at [https://example.com/contact](https://example.com/contact).

Thank you for using Visualwise-Jules!
