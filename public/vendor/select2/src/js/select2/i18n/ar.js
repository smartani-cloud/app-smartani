<<<<<<< HEAD
define(function () {
  // Arabic
  return {
    errorLoading: function () {
      return 'لا يمكن تحميل النتائج';
    },
    inputTooLong: function (args) {
      var overChars = args.input.length - args.maximum;

      return 'الرجاء حذف ' + overChars + ' عناصر';
    },
    inputTooShort: function (args) {
      var remainingChars = args.minimum - args.input.length;

      return 'الرجاء إضافة ' + remainingChars + ' عناصر';
    },
    loadingMore: function () {
      return 'جاري تحميل نتائج إضافية...';
    },
    maximumSelected: function (args) {
      return 'تستطيع إختيار ' + args.maximum + ' بنود فقط';
    },
    noResults: function () {
      return 'لم يتم العثور على أي نتائج';
    },
    searching: function () {
      return 'جاري البحث…';
    },
     removeAllItems: function () {
      return 'قم بإزالة كل العناصر';
    }
  };
=======
define(function () {
  // Arabic
  return {
    errorLoading: function () {
      return 'لا يمكن تحميل النتائج';
    },
    inputTooLong: function (args) {
      var overChars = args.input.length - args.maximum;

      return 'الرجاء حذف ' + overChars + ' عناصر';
    },
    inputTooShort: function (args) {
      var remainingChars = args.minimum - args.input.length;

      return 'الرجاء إضافة ' + remainingChars + ' عناصر';
    },
    loadingMore: function () {
      return 'جاري تحميل نتائج إضافية...';
    },
    maximumSelected: function (args) {
      return 'تستطيع إختيار ' + args.maximum + ' بنود فقط';
    },
    noResults: function () {
      return 'لم يتم العثور على أي نتائج';
    },
    searching: function () {
      return 'جاري البحث…';
    },
     removeAllItems: function () {
      return 'قم بإزالة كل العناصر';
    }
  };
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
});