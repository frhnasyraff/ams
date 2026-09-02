/*
jQuery.ganttView v.0.8.8
Copyright (c) 2010 JC Grubbs - jc.grubbs@devmynd.com
MIT License Applies
*/

/*
Options
-----------------
showWeekends: boolean
data: object
cellWidth: number
cellHeight: number
slideWidth: number
dataUrl: string
behavior: {
	clickable: boolean,
	draggable: boolean,
	resizable: boolean,
	onClick: function,
	onDrag: function,
	onResize: function
}
*/

(function (jQuery) {
  jQuery.fn.ganttView = function () {
    var args = Array.prototype.slice.call(arguments);

    if (args.length == 1 && typeof args[0] == 'object') {
      build.call(this, args[0]);
    }

    if (args.length == 2 && typeof args[0] == 'string') {
      handleMethod.call(this, args[0], args[1]);
    }
  };

  function build(options) {
    var els = this;
    var defaults = {
      showWeekends: true,
      cellWidth: 30,
      cellHeight: 31,
      slideWidth: 400,
      vHeaderWidth: 100,
      behavior: {
        clickable: true,
        draggable: true,
        resizable: true
      }
    };

    var opts = jQuery.extend(true, defaults, options);

    if (opts.data) {
      build();
    } else if (opts.dataUrl) {
      jQuery.getJSON(opts.dataUrl, function (data) {
        opts.data = data;
        build();
      });
    }

    function daysInMonth(month, year) {
      return new Date(year, month, 0).getDate();
    }

    function build() {
      var data_Date = opts.data[0].series[0].start;

      const month = data_Date.getMonth() + 1;
      const year = data_Date.getFullYear();
      var num_Days = daysInMonth(month, year);

      const month_start_date = new Date(year, data_Date.getMonth(), 01);
      const month_end_date = new Date(year, data_Date.getMonth(), num_Days);

      // var minDays = Math.floor(opts.slideWidth / num_Days);
      var startEnd = DateUtils.getBoundaryDatesFromData(opts.data, num_Days);

      opts.start = startEnd[0];
      opts.end = startEnd[1];

      els.each(function () {
        var container = jQuery(this);
        var div = jQuery('<div>', { class: 'ganttview' });
        new Chart(div, opts, month_start_date, month_end_date, num_Days).render();
        container.append(div);

        var w =
          jQuery('div.ganttview-vtheader', container).outerWidth() +
          jQuery('div.ganttview-slide-container', container).outerWidth();
        container.css('width', w + 2 + 'px');
        new Behavior(container, opts).apply();
      });
    }
  }

  function handleMethod(method, value) {
    if (method == 'setSlideWidth') {
      var div = $('div.ganttview', this);
      div.each(function () {
        var vtWidth = $('div.ganttview-vtheader', div).outerWidth();
        $(div).width(vtWidth + value);
        $('div.ganttview-slide-container', this).width(value);
      });
    }
  }

  var Chart = function (div, opts, month_start_date, month_end_date, num_Days) {
    function render() {
      addVtHeader(div, opts.data, opts.cellHeight);

      var slideDiv = jQuery('<div>', {
        class: 'ganttview-slide-container',
        css: { width: opts.slideWidth + 'px' }
      });

      dates = getDates(opts.start, opts.end);
      const headerDates = getDates(month_start_date, month_end_date);
      addHzHeader(slideDiv, headerDates, opts.cellWidth, month_start_date);
      addGrid(slideDiv, opts.data, dates, opts.cellWidth, num_Days);
      addBlockContainers(slideDiv, opts.data);
      addBlocks(slideDiv, opts.data, opts.cellWidth, opts.start);
      div.append(slideDiv);
      //applyLastClass(div.parent());
    }

    var monthNames = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December'
    ];

    // Creates a 3 dimensional array [year][month][day] of every day
    // between the given start and end dates
    function getDates(start, end) {
      var dates = [];
      dates[start.getFullYear()] = [];
      dates[start.getFullYear()][start.getMonth()] = [start];
      var last = start;
      while (last.compareTo(end) == -1) {
        var next = last.clone().addDays(1);
        if (!dates[next.getFullYear()]) {
          dates[next.getFullYear()] = [];
        }
        if (!dates[next.getFullYear()][next.getMonth()]) {
          dates[next.getFullYear()][next.getMonth()] = [];
        }
        dates[next.getFullYear()][next.getMonth()].push(next);
        last = next;
      }
      return dates;
    }

    function addVtHeader(div, data, cellHeight) {
      var headerDiv = jQuery('<div>', { class: 'ganttview-vtheader' });
      for (var i = 0; i < data.length; i++) {
        var itemDiv = jQuery('<div>', { class: 'ganttview-vtheader-item' });
        itemDiv.append(
          jQuery('<div>', {
            id: 'ganttview-vtheader-item-name',
            class: 'ganttview-vtheader-item-name',
            css: { height: data[i].series.length * cellHeight + 'px', cursor: 'pointer' },
            title: 'start:' + data[i].start_date + ' End:' + data[i].end_date
          }).append('<a class="small" href="service_requests/info?id=' + data[i].id + '">' + data[i].name + '</a>')
        );

        var seriesDiv = jQuery('<div>', { class: 'ganttview-vtheader-series' });
        for (var j = 0; j < data[i].series.length; j++) {
          seriesDiv.append(
            jQuery('<div>', {
              class: 'ganttview-vtheader-series-name small',
              title: 'status:' + data[i].status
            }).append(data[i].series[j].name)
          );
        }
        itemDiv.append(seriesDiv);
        headerDiv.append(itemDiv);
      }
      div.append(headerDiv);
    }

    function addHzHeader(div, dates, cellWidth, month_start_date) {
      var headerDiv = jQuery('<div>', { class: 'ganttview-hzheader' });
      var monthsDiv = jQuery('<div>', { class: 'ganttview-hzheader-months' });
      var daysDiv = jQuery('<div>', { class: 'ganttview-hzheader-days' });
      var totalW = 0;

      const m_val = month_start_date.getMonth();

      monthsDiv.append(
        jQuery('<div>', {
          class: 'ganttview-hzheader-month'
        }).append(monthNames[m_val] + ' - ' + month_start_date.getFullYear())
      );

      for (var y in dates) {
        for (var m in dates[y]) {
          var w = dates[y][m].length * cellWidth;
          totalW = totalW + w;

          for (var d in dates[y][m]) {
            daysDiv.append(jQuery('<div>', { class: 'ganttview-hzheader-day' }).append(dates[y][m][d].getDate()));
          }
        }
      }
      //monthsDiv.css('width', totalW + 'px');
      //daysDiv.css('width', totalW + 'px');
      headerDiv.append(monthsDiv).append(daysDiv);
      div.append(headerDiv);
    }

    function addGrid(div, data, dates, cellWidth, num_Days) {
      var gridDiv = jQuery('<div>', { class: 'ganttview-grid' });
      var rowDiv = jQuery('<div>', { class: 'ganttview-grid-row' });
      var clearFix = jQuery('<div>', { class: 'clearfix' });

      for (let i = 0; i < num_Days; i++) {
        var cellDiv = jQuery('<div>', { class: 'ganttview-grid-row-cell' });
        rowDiv.append(cellDiv);
      }

      var w = jQuery('div.ganttview-grid-row-cell', rowDiv).length * cellWidth;
      rowDiv.css('width', w + 'px');
      //gridDiv.css('width', w + 'px');
      rowDiv.append(clearFix);
      for (var i = 0; i < data.length; i++) {
        for (var j = 0; j < data[i].series.length; j++) {
          gridDiv.append(rowDiv.clone());
        }
      }
      div.append(gridDiv);
    }

    function addBlockContainers(div, data) {
      var blocksDiv = jQuery('<div>', { class: 'ganttview-blocks' });
      for (var i = 0; i < data.length; i++) {
        for (var j = 0; j < data[i].series.length; j++) {
          blocksDiv.append(jQuery('<div>', { class: 'ganttview-block-container' }));
        }
      }
      div.append(blocksDiv);
    }

    function addBlocks(div, data, cellWidth, start) {
      var rows = jQuery('div.ganttview-blocks div.ganttview-block-container', div);
      var rowIdx = 0;
      var from_start = 0;
      for (var i = 0; i < data.length; i++) {
        for (var j = 0; j < data[i].series.length; j++) {
          var series = data[i].series[j];
          var size = DateUtils.daysBetween(series.start, series.end) + 1;
          var offset = DateUtils.daysBetween(start, series.start);

          from_start = series.start.getDate() - 1;
          const blockWidth = 25;
          var block = jQuery('<div>', {
            class: 'ganttview-block',
            title: data[i].title,
            css: {
              width: size * blockWidth + 'px',
              'margin-left': from_start * blockWidth + 'px'
            }
          });
          addBlockData(block, data[i], series);
          if (data[i].series[j].color) {
            block.css('background-color', data[i].series[j].color);
          }
          block.append(jQuery('<div>', { class: 'ganttview-block-text' }).text(size));
          jQuery(rows[rowIdx]).append(block);
          rowIdx = rowIdx + 1;
        }
      }
    }

    function addBlockData(block, data, series) {
      // This allows custom attributes to be added to the series data objects
      // and makes them available to the 'data' argument of click, resize, and drag handlers
      var blockData = { id: data.id, name: data.name };
      jQuery.extend(blockData, series);
      block.data('block-data', blockData);
    }

    function applyLastClass(div) {
      jQuery('div.ganttview-grid-row div.ganttview-grid-row-cell:last-child', div).addClass('last');
      jQuery('div.ganttview-hzheader-days div.ganttview-hzheader-day:last-child', div).addClass('last');
      jQuery('div.ganttview-hzheader-months div.ganttview-hzheader-month:last-child', div).addClass('last');
    }

    return {
      render: render
    };
  };

  var Behavior = function (div, opts) {
    function apply() {
      if (opts.behavior.clickable) {
        bindBlockClick(div, opts.behavior.onClick);
      }

      if (opts.behavior.resizable) {
        bindBlockResize(div, opts.cellWidth, opts.start, opts.behavior.onResize);
      }

      if (opts.behavior.draggable) {
        bindBlockDrag(div, opts.cellWidth, opts.start, opts.behavior.onDrag);
      }
    }

    function bindBlockClick(div, callback) {
      jQuery('div.ganttview-block', div).live('click', function () {
        if (callback) {
          callback(jQuery(this).data('block-data'));
        }
      });
    }

    function bindBlockResize(div, cellWidth, startDate, callback) {
      jQuery('div.ganttview-block', div).resizable({
        grid: cellWidth,
        handles: 'e,w',
        stop: function () {
          var block = jQuery(this);
          updateDataAndPosition(div, block, cellWidth, startDate);
          if (callback) {
            callback(block.data('block-data'));
          }
        }
      });
    }

    function bindBlockDrag(div, cellWidth, startDate, callback) {
      jQuery('div.ganttview-block', div).draggable({
        axis: 'x',
        grid: [cellWidth, cellWidth],
        stop: function () {
          var block = jQuery(this);
          updateDataAndPosition(div, block, cellWidth, startDate);
          if (callback) {
            callback(block.data('block-data'));
          }
        }
      });
    }

    function updateDataAndPosition(div, block, cellWidth, startDate) {
      var container = jQuery('div.ganttview-slide-container', div);
      var scroll = container.scrollLeft();
      var offset = block.offset().left - container.offset().left - 1 + scroll;

      // Set new start date
      var daysFromStart = Math.round(offset / cellWidth);
      var newStart = startDate.clone().addDays(daysFromStart);
      block.data('block-data').start = newStart;

      // Set new end date
      var width = block.outerWidth();
      var numberOfDays = Math.round(width / cellWidth) - 1;
      block.data('block-data').end = newStart.clone().addDays(numberOfDays);
      //jQuery("div.ganttview-block-text", block).text(numberOfDays + 1);

      // Remove top and left properties to avoid incorrect block positioning,
      // set position to relative to keep blocks relative to scrollbar when scrolling
      block
        .css('top', '')
        .css('left', '')
        .css('position', 'relative')
        .css('margin-left', offset + 'px');
    }

    return {
      apply: apply
    };
  };

  var ArrayUtils = {
    contains: function (arr, obj) {
      var has = false;
      for (var i = 0; i < arr.length; i++) {
        if (arr[i] == obj) {
          has = true;
        }
      }
      return has;
    }
  };

  var DateUtils = {
    daysBetween: function (start, end) {
      if (!start || !end) {
        return 0;
      }
      start = Date.parse(start);
      end = Date.parse(end);
      if (start.getYear() == 1901 || end.getYear() == 8099) {
        return 0;
      }
      var count = 0,
        date = start.clone();
      while (date.compareTo(end) == -1) {
        count = count + 1;
        date.addDays(1);
      }
      return count;
    },

    isWeekend: function (date) {
      return date.getDay() % 6 == 0;
    },

    getBoundaryDatesFromData: function (data, minDays) {
      var minStart = new Date();
      maxEnd = new Date();
      for (var i = 0; i < data.length; i++) {
        for (var j = 0; j < data[i].series.length; j++) {
          var start = Date.parse(data[i].series[j].start);
          var end = Date.parse(data[i].series[j].end);
          if (i == 0 && j == 0) {
            minStart = start;
            maxEnd = end;
          }
          if (minStart.compareTo(start) == 1) {
            minStart = start;
          }
          if (maxEnd.compareTo(end) == -1) {
            maxEnd = end;
          }
        }
      }

      // Insure that the width of the chart is at least the slide width to avoid empty
      // whitespace to the right of the grid
      if (DateUtils.daysBetween(minStart, maxEnd) < minDays) {
        maxEnd = minStart.clone().addDays(minDays);
      }

      return [minStart, maxEnd];
    }
  };
})(jQuery);
