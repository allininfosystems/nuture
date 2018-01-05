var WIO_FORUM_BOOKMARKS = {

  meTopicIds: {},
  linkBookmarkId: 'wio-forum-bookmarks-link',
  emptyBookmarkId: 'wio-forum-zero-bookmarks',
  bookmarkFormId: 'wio-forum-bookmarks-form',
  meCookieNameStr: 'wio-forum-bookmarks-saved',
  bookmarkTableId: 'wio-forum-table-bookmark',
  idRowBegin: 'wio-forum-row-id-',
  defaultDays: 30,

  addBookMark: function(id) {
    this.getAllBookmarks();
    this.meTopicIds[id] = id;
    this.saveBookmark();
  },

  saveBookmark: function() {
    var cookieStr = JSON.stringify(this.meTopicIds);
    this.createCookie(this.meCookieNameStr, cookieStr, this.defaultDays);
    this.refreshBookmarks();
  },

  removeBookmark: function(id) {
    this.getAllBookmarks();
    delete this.meTopicIds[id];
    this.saveBookmark();
    this.deleteRowBookmark(id);
  },

  getAllBookmarks: function() {
    var cookieStr = this.readCookie(this.meCookieNameStr);
    if (cookieStr) {
      this.meTopicIds = JSON.parse(cookieStr);
    }
    return this.meTopicIds;
  },

  refreshBookmarks: function() {
    this.showBookmarks();
  },

  showBookmarks: function() {
    this.getAllBookmarks();
    var zeroBookmark = document.getElementById(this.emptyBookmarkId);
    var bookmarksLink = document.getElementById(this.linkBookmarkId);
    if (!bookmarksLink || !zeroBookmark) {
      return;
    }
    if (this.meTopicIds && Object.keys(this.meTopicIds).length) {
      bookmarksLink.innerHTML = Object.keys(this.meTopicIds).length;
      zeroBookmark.style.display = 'none';
      bookmarksLink.style.display = 'inline-block';
    } else {
      zeroBookmark.style.display = 'inline-block';
      bookmarksLink.style.display = 'none';
    }
  },

  goToBookmarkPage: function() {
    this.getAllBookmarks();
    var form = document.getElementById(this.bookmarkFormId);
    if (this.meTopicIds && Object.keys(this.meTopicIds).length) {
      var formHtml = '';
      for (var a in this.meTopicIds) {
        formHtml += '<input type="hidden" name="topic_ids[]" value="' + parseInt(this.meTopicIds[a]) + '" />'
      }
      form.innerHTML = formHtml;
      form.submit();
    }
    return false;
  },

  deleteRowBookmark: function(_id) {
    var table = document.getElementById(this.bookmarkTableId);
    var row = document.getElementById(this.idRowBegin + _id);
    if (!table || !row) {
      return;
    }
    table.deleteRow(row.rowIndex);
  },

  createCookie: function(name, value, days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
  },

  readCookie: function(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  },

  eraseCookie: function(name) {
    createCookie(name, "", -1);
  }

}

WIO_Forum_Responsive_tables = { 
  className: 'data-table-forum-resp',
  init: function() {
    var tables = document.getElementsByClassName(this.className);
    for(var a=0; tables.length > a; a++) {
      var table = tables[a];
      this.updateTable(table);
    }
  },
  updateTable: function(table) {
    var names = this.getNames(table);
    var tbody = table.getElementsByTagName('tbody')[0];
    if(!tbody) {
      return;
    }
    var rows = tbody.getElementsByTagName('tr');
    for(var a=0; rows.length > a; a++) {
      var row = rows[a];
      this.updateRow(row, names);
    }
  },
  getNames: function(table) {
    var names = [];
    var ths = table.getElementsByTagName('th');
    for(var a=0; ths.length > a; a++) {
      names.push(ths[a].innerHTML);
    }
    return names;
  },
  updateRow(tr, names) {
    var tds = tr.getElementsByTagName('td');
    for(var a=0; tds.length > a; a++) {
      var td = tds[a];
      var htmlIn = td.innerHTML;
      if(names[a]) {
        var newHTML = '<div class="attr-title" data-title="' + names[a] + '">' + htmlIn + '</div>';
        td.innerHTML = newHTML;
      }
    }
  }
}