package com.dhanuka.indhan.util

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Context
import android.content.DialogInterface
import android.content.res.Resources
import android.net.Uri
import android.os.Build
import android.os.Environment
import android.os.storage.StorageManager
import android.text.TextUtils
import android.util.Log
import android.util.Patterns
import android.util.TypedValue
import android.view.View
import android.view.ViewGroup
import android.view.inputmethod.InputMethodManager
import android.widget.DatePicker
import android.widget.EditText
import android.widget.Toast
import com.dhanuka.indhan.App.appContext
import com.dhanuka.indhan.BuildConfig
import com.dhanuka.indhan.analytics.FAEvent
import com.dhanuka.indhan.analytics.FATracker
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.google.firebase.storage.FirebaseStorage
import com.google.firebase.storage.StorageReference
import com.google.firebase.storage.UploadTask
import org.joda.time.DateTime
import org.joda.time.format.DateTimeFormat
import java.io.File
import java.io.FileInputStream
import java.io.FileOutputStream
import java.io.IOException
import java.math.RoundingMode
import java.text.DecimalFormat
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*


/**
 * Created by Shivam on 3/13/2018.
 */
object Utility {
    @JvmStatic
    fun scheduleJob(context: Context?) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            //abhilasha stop service
            /*      ComponentName serviceComponent = new ComponentName(context, ScheduledJobService.class);
            JobInfo.Builder builder = null;
            builder = new JobInfo.Builder(0, serviceComponent);
            builder.setMinimumLatency(1000 * 60 * 20);    // wait at least
            builder.setOverrideDeadline(1000 * 60 * 20); // maximum delay
            //builder.setMinimumLatency(10 * 1000);    // wait at least
            //builder.setOverrideDeadline(300 * 1000); // maximum delay
            //builder.setRequiredNetworkType(JobInfo.NETWORK_TYPE_UNMETERED); // require unmetered network
            //builder.setRequiresDeviceIdle(true); // device should be idle
            //builder.setRequiresCharging(false); // we don't care if the device is charging or not

            JobScheduler jobScheduler = (JobScheduler) context.getApplicationContext().getSystemService(context.JOB_SCHEDULER_SERVICE);
            jobScheduler.schedule(builder.build());*/
            //abhilasha stop service
        } else {
            Log.e("called", "normal service")
            //abhilasha stop service
            //  context.startService(new Intent(context, ImprovisedScheduleService.class));
        }
    }

    /*   public static void saveSelectedNumber(Context context, LogObject log) {
        SelectedNumberModel model = Preference.getPreferenceSelected(context, "selectednum");

        if (model == null) {
            model = new SelectedNumberModel();
        }

        String strLog = log.getNumber() + String.valueOf(log.getDate());

        if (!model.getSelectedNumbers().contains(strLog)) {
            model.getSelectedNumbers().add(strLog);
        }
        Preference.addPreferenceSelected(context, "selectednum", model);
    }*/
    /*   public static void removeSelectedNumber(Context context, LogObject log) {
        SelectedNumberModel model = Preference.getPreferenceSelected(context, "selectednum");

        if (model == null) {
            model = new SelectedNumberModel();
        }

        String strLog = log.getNumber() + String.valueOf(log.getDate());

        if (model.getSelectedNumbers().contains(strLog)) {
            model.getSelectedNumbers().remove(strLog);
        }
        Preference.addPreferenceSelected(context, "selectednum", model);
    }*/
    @JvmStatic
    fun getDatefromFormat(pre_date: String?): String? {
        val formatter1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
        val dater = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        var date: Date? = null
        date = try {
            formatter1.parse(pre_date)
        } catch (e: Exception) {
            e.printStackTrace()
            return null
        }
        return dater.format(date)
    }

    val currentDate: String
        get() {
            val formatter1 =
                SimpleDateFormat("yyyyMMdd/HHmm", Locale.ENGLISH)
            return formatter1.format(Date())
        }

    @JvmStatic
    fun getMontfromDate(pre_date: String?): Int {
        val formatter1 = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        val dater = SimpleDateFormat("MM", Locale.ENGLISH)
        var date: Date? = null
        date = try {
            formatter1.parse(pre_date)
        } catch (e: Exception) {
            e.printStackTrace()
            return 0
        }
        return dater.format(date).toInt()
    }

    fun getDateFromMonth(pre_date: String?): Date? {
        val formatter1 = SimpleDateFormat("yyyy-MM", Locale.ENGLISH)
        var date: Date? = null
        try {
            date = formatter1.parse(pre_date)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return date
    }

    fun month_and_year(date: Date?): String {
        val dater = SimpleDateFormat("MMMM yyyy", Locale.ENGLISH)
        return dater.format(date)
    }

    fun getMontfromDateObj(date: Date?): Int {
        val dater = SimpleDateFormat("MM", Locale.ENGLISH)
        return dater.format(date).toInt()
    }

    val monthnYear: String
        get() {
            val dater = SimpleDateFormat("MMMM yyyy", Locale.ENGLISH)
            val date = Date()
            return dater.format(date)
        }

    @JvmStatic
    fun getTimefromFormat(pre_date: String?): String? {
        val formatter1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
        val dater = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        val time = SimpleDateFormat("hh:mm:ss aa", Locale.ENGLISH)
        var date: Date? = null
        date = try {
            formatter1.parse(pre_date)
        } catch (e: Exception) {
            e.printStackTrace()
            return null
        }
        return time.format(date)
    }

    fun checkIsValid(dateOfbean: String?): Int {
        val currentDate = Date()
        val formatter1 =
            SimpleDateFormat("EEEE, MMMM dd yyyy", Locale.ENGLISH)
        //SimpleDateFormat formatter1 = new SimpleDateFormat("EEEE, MMMM dd yyyy, hh:mm aa");
        var date: Date? = null
        date = try {
            formatter1.parse(dateOfbean!!)
        } catch (e: Exception) {
            e.printStackTrace()
            return 0
        }
        //        int val = DateTimeComparator.getDateOnlyInstance().compare(date, currentDate);
        return date!!.compareTo(currentDate)
    }

    fun compareDate(dateOfbean: Date): Int {
        val sd = SimpleDateFormat("dd-MM-yyyy", Locale.ENGLISH)
        val date = sd.format(Date())
        var currentDate: Date? = null
        try {
            currentDate = sd.parse(date)
        } catch (e: ParseException) {
            e.printStackTrace()
        }
        return dateOfbean.compareTo(currentDate)
    }

    fun getList(n: Int): ArrayList<String> {
        val list = ArrayList<String>()
        for (i in 1..n) {
            list.add(i.toString())
        }
        return list
    }

    fun currentMonthNo(): Int {
        val sdf = SimpleDateFormat("MM", Locale.ENGLISH)
        return sdf.format(Date()).toInt()
    }

    val monthList: ArrayList<String>
        get() {
            val sdf = SimpleDateFormat("MM", Locale.ENGLISH)
            var value = sdf.format(Date()).toInt()
            val list = ArrayList<String>()
            val test = ArrayList<String>()
            list.add("January")
            list.add("February")
            list.add("March")
            list.add("April")
            list.add("May")
            list.add("June")
            list.add("July")
            list.add("August")
            list.add("September")
            list.add("October")
            list.add("November")
            list.add("December")
            test.add(list[value - 1])
            if (value == 12) {
                value = 0
            }
            test.add(list[value])
            return test
        }
    val dataListofTestimonial: ArrayList<String>
        get() {
            val list = ArrayList<String>()
            list.add("Kohinoor")
            list.add("Farmer")
            list.add("Channel")
            list.add("Others")
            return list
        }
    val rating: ArrayList<String>
        get() {
            val list = ArrayList<String>()
            list.add("No personal relation with this channel")
            list.add("Average")
            list.add("Good")
            list.add("Very good")
            list.add("Excellent")
            return list
        }
    val financialYear: String
        get() {
            val d = Date()
            val calendar = Calendar.getInstance(Locale.ENGLISH)
            calendar.time = d
            val month = calendar[Calendar.MONTH]
            val y = if (month < 3) calendar[Calendar.YEAR] - 1 else calendar[Calendar.YEAR]
            return "Financial Year : " + y + "-" + (y + 1)
        }

    /**
     * ccDates
     *
     * @param datetimeformat
     * @return
     */
    fun getDateFromCCDateTimeFormat(datetimeformat: String?): Date? {
        var d: Date? = null
        val sdf = SimpleDateFormat("EEEE, MMMM dd yyyy, hh:mm aa", Locale.ENGLISH)
        //SimpleDateFormat sdf2 = new SimpleDateFormat("yyyy-MM-dd");
        try {
            d = sdf.parse(datetimeformat)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return d
    }

    fun getDateFromsfDateTimeFormat(datetimeformat: String?): Date? {
        var d: Date? = null
        val sdf = SimpleDateFormat("EEEE, MMMM dd yyyy", Locale.ENGLISH)
        //SimpleDateFormat sdf2 = new SimpleDateFormat("yyyy-MM-dd");
        try {
            d = sdf.parse(datetimeformat)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return d
    }

    fun getDateStringforCC(dateOfbean: Date?): String {
        val sd = SimpleDateFormat("MMMM dd, yyyy", Locale.ENGLISH)
        return sd.format(Date())
    }

    fun getTimeStringforCC(dateOfbean: Date?): String {
        val sd = SimpleDateFormat("hh:mm aa", Locale.ENGLISH)
        return sd.format(Date())
    }

    fun getTimeFromString(dateOfbean: String): Date? {
        var sdf: SimpleDateFormat? = null
        sdf =
            if (dateOfbean.length < 10) SimpleDateFormat(
                "hh:mm aa",
                Locale.ENGLISH
            ) else SimpleDateFormat("EEEE, MMMM dd yyyy, hh:mm aa", Locale.ENGLISH)
        var date: Date? = null
        try {
            date = sdf.parse(dateOfbean)
        } catch (e: ParseException) {
            e.printStackTrace()
        }
        return date
    }

    fun getDateObj(date: String?): Date? {
        var date_obj: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        try {
            date_obj = sdf.parse(date)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return date_obj
    }

    fun getMonthNo(month: String?): Int {
        when (month) {
            "January" -> return 1
            "February" -> return 2
            "March" -> return 3
            "April" -> return 4
            "May" -> return 5
            "June" -> return 6
            "July" -> return 7
            "August" -> return 8
            "September" -> return 9
            "October" -> return 10
            "November" -> return 11
            "December" -> return 12
        }
        return 0
    }

    fun getMonth(month: Int): String {
        //  if(month==0) month=1;
        val monthList = arrayOf(
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        )
        return monthList[month]
    }

    fun getDateForView(date: String?): String {
        var d: Date? = null
        val sdf = SimpleDateFormat("EEEE, MMM dd yyyy", Locale.ENGLISH)
        val sdf2 = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        try {
            d = sdf2.parse(date)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return sdf.format(d)
    }

    fun getDateForerrorView(date: String?): String {
        var d: Date? = null

        var time = ""
        if (!TextUtils.isEmpty(date)) {
            val sdf = SimpleDateFormat("EEE, MMM dd yyyy", Locale.ENGLISH)
            val sdf2 = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZ", Locale.ENGLISH)
            /*DateTime jodaTime = DateTime.parse(date);
        sdf.format(jodaTime);
        */try {
                d = sdf2.parse(date)
                time = sdf.format(d)
            } catch (e: Exception) {
                time = ""
                e.printStackTrace()
            }
        }
        return time
    }

    fun getDateForerrorViewNew(date: String?): String {
        var d: Date? = null
        var time = ""
        if (!TextUtils.isEmpty(date)) {
            val sdf = SimpleDateFormat("EEEE, MMM dd yyyy", Locale.ENGLISH)
            val sdf2 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
            try {
                d = sdf2.parse(date)
                time = sdf.format(d)
            } catch (e: Exception) {
                time = ""
                e.printStackTrace()
            }
        }
        return time
    }

    fun getDate(date: String): String? {
        val sdf = SimpleDateFormat("EEEE, MMM dd yyyy, hh:mm a", Locale.ENGLISH)
        val sdf2 = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss", Locale.ENGLISH)
        try {
            return sdf.format(sdf2.parse(date))
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return null
    }


    fun getDateStrFromJoda(date: String?): String {
        var d: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        val sdf2 = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS", Locale.ENGLISH)
        try {
            d = sdf2.parse(date)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return sdf.format(d)
    }

    fun getDateTimeStrFromJoda(date: String?): Long {
        var d: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
        try {
            d = sdf.parse(date)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return d?.time ?: 0
    }

    fun getDateTimeStr(date: String?): Date {
        var d: Date? = null
        var sdf = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZZZ", Locale.ENGLISH)
        val strDate: Date = sdf.parse(date)
        sdf = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
        val date = sdf.format(strDate)
        try {
            d = sdf.parse(date)
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return d ?: Date()
    }

    fun roundOffDecimal(number: Double): Double {
        val df = DecimalFormat("#.##")
        df.roundingMode = RoundingMode.FLOOR
        return df.format(number).toDouble()
    }

    fun hideKeyboardFrom(context: Context, view: View) {
        val imm: InputMethodManager =
            context.getSystemService(Activity.INPUT_METHOD_SERVICE) as InputMethodManager
        imm.hideSoftInputFromWindow(view.windowToken, 0)
    }

    fun formattedDate(date: String?): String? {
        val dateTime = DateTimeFormat.forPattern("yyyy-MM-dd HH:mm:ss").parseDateTime(date)
        val dateTime2 = SimpleDateFormat("dd MMM, yyyy hh:mm a", Locale.ENGLISH)
        val dateTime3 = SimpleDateFormat("hh:mm a", Locale.ENGLISH)
        val today = DateTime()
        val yesterday = today.minusDays(1)
        val twodaysago = today.minusDays(2)
        val tomorrow = today.minusDays(-1)
        return when {
            dateTime.toLocalDate() == today.toLocalDate() -> {
                "Today ${dateTime3.format(dateTime.toDate().time)} "
            }
            dateTime.toLocalDate() == yesterday.toLocalDate() -> {
                "Yesterday ${dateTime3.format(dateTime.toDate().time)} "
            }
            dateTime.toLocalDate() == twodaysago.toLocalDate() -> {
                "2 days ago ${dateTime3.format(dateTime.toDate().time)} "
            }
            else -> {
                dateTime2.format(dateTime.toDate().time)
            }
        }
    }

    fun setupUI(activity: Activity, view: View) {
        // Set up touch listener for non-text box views to hide keyboard.
        if (view !is EditText) {
            view.setOnTouchListener { v, _ ->
                val inputMethodManager =
                    activity.getSystemService(Activity.INPUT_METHOD_SERVICE) as InputMethodManager
                inputMethodManager.hideSoftInputFromWindow(activity.currentFocus!!.windowToken, 0)
                false
            }
        }

        //If a layout container, iterate over children and seed recursion.
        if (view is ViewGroup) {
            for (i in 0 until view.childCount) {
                val innerView = view.getChildAt(i)
                setupUI(activity, innerView)
            }
        }
    }

    fun getPickedDate(context: Context?, datePickCallBack: DatePickCallBack) {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val mYear = c[Calendar.YEAR]
        val mMonth = c[Calendar.MONTH]
        val mDay = c[Calendar.DAY_OF_MONTH]
        val dpd = DatePickerDialog(
            context!!,
            { view: DatePicker?, year: Int, monthOfYear: Int, dayOfMonth: Int ->
                if (validateNextDate(context, dayOfMonth, monthOfYear, year)) {
                    val dd: String
                    var yy: String
                    val mm: String
                    mm =
                        if (monthOfYear + 1 < 10) "0" + (monthOfYear + 1) else (monthOfYear + 1).toString()
                    dd = if (dayOfMonth < 10) "0$dayOfMonth" else dayOfMonth.toString() + ""
                    datePickCallBack.onDateSet(
                        "$year-$mm-$dd",
                        formatDate(year, mm.toInt(), dd.toInt())
                    )
                }
            },
            mYear,
            mMonth,
            mDay
        )
        val calendar = Calendar.getInstance(Locale.ENGLISH)
        calendar[mYear, mMonth] = mDay
        dpd.datePicker.maxDate = calendar.timeInMillis
        dpd.setOnCancelListener { dialog: DialogInterface? -> datePickCallBack.onCancel() }
        dpd.show()
    }

    fun getPickedFutureDate(context: Context?, datePickCallBack: DatePickCallBack) {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val mYear = c[Calendar.YEAR]
        val mMonth = c[Calendar.MONTH]
        val mDay = c[Calendar.DAY_OF_MONTH]
        val dpd = DatePickerDialog(
            context!!,
            { view: DatePicker?, year: Int, monthOfYear: Int, dayOfMonth: Int ->
                if (validateNextDate(context, dayOfMonth, monthOfYear, year)) {
                    val dd: String
                    var yy: String
                    val mm: String
                    mm =
                        if (monthOfYear + 1 < 10) "0" + (monthOfYear + 1) else (monthOfYear + 1).toString()
                    dd = if (dayOfMonth < 10) "0$dayOfMonth" else dayOfMonth.toString() + ""
                    datePickCallBack.onDateSet(
                        "$year-$mm-$dd",
                        formatDate(year, mm.toInt(), dd.toInt())
                    )
                }
            },
            mYear,
            mMonth,
            mDay
        )
        val calendar = Calendar.getInstance(Locale.ENGLISH)
        calendar[mYear, mMonth] = mDay
        dpd.datePicker.minDate = calendar.timeInMillis
        dpd.setOnCancelListener { dialog: DialogInterface? -> datePickCallBack.onCancel() }
        dpd.show()
    }

    fun formatDate(year: Int, month: Int, day: Int): String {
        return try {
            var format =
                SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
            var newDate: Date? = null
            newDate = format.parse("$year-$month-$day")
            format = SimpleDateFormat("dd-MMM-yyyy", Locale.ENGLISH)
            format.format(newDate)
        } catch (e: Exception) {
            e.printStackTrace()
            ""
        }
    }

    private fun validateNextDate(mContext: Context?, day: Int, month: Int, year: Int): Boolean {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val currentYear = c[Calendar.YEAR]
        val currentMonth = c[Calendar.MONTH] + 1
        val currentDay = c[Calendar.DAY_OF_MONTH]
        return when {
            (day > currentDay) and (year == currentYear) and (month == currentMonth) -> {
                Toast.makeText(mContext, "Please select a valid date", Toast.LENGTH_LONG).show()
                false
            }
            (month > currentMonth && year == currentYear) -> {
                Toast.makeText(mContext, "Please select valid month", Toast.LENGTH_LONG).show()
                false
            }
            year > currentYear -> {
                Toast.makeText(mContext, "Please select valid year", Toast.LENGTH_LONG).show()
                false
            }
            else -> true
        }
    }

    fun isValidMobile(phone: String?): Boolean {
        return Patterns.PHONE.matcher(phone).matches()
    }

    fun isValidMail(email: String?): Boolean {
        return Patterns.EMAIL_ADDRESS.matcher(email).matches()
    }


    fun findDifference(sdf: SimpleDateFormat, start_date: String?, end_date: String?): Double {
        var diff = 0.0
        diff = try {
            val d1 = sdf.parse(start_date)
            val d2 = sdf.parse(end_date)
            val difference_In_Time = (d2?.time ?: 0) - (d1?.time ?: 0)
            val difference_In_Minutes = difference_In_Time / (1000 * 60) % 60
            val difference_In_Hours = difference_In_Time / (1000 * 60 * 60) % 24
            difference_In_Hours + difference_In_Minutes * 0.0166667
        } // Catch the Exception
        catch (e: ParseException) {
            e.printStackTrace()
            return 0.0
        }
        return diff
    }

    fun getDateForArrivalDeparture(date: String?, inFormat: String, outFormat: String): String? {
        var d: Date? = null
        var time = ""
        if (!TextUtils.isEmpty(date)) {
            val sdf = SimpleDateFormat(inFormat, Locale.ENGLISH)
            val sdf2 = SimpleDateFormat(outFormat, Locale.ENGLISH)
            try {
                d = sdf.parse(date)
                time = sdf2.format(d)
            } catch (e: Exception) {
                time = ""
                e.printStackTrace()
            }
        }
        return time
    }

    fun getDateCurrent(plusDays: DateTime): String? {
        return SimpleDateFormat("dd MMMM, yyyy", Locale.ENGLISH).format(plusDays.toDate().time)
    }

    fun getDateCurrent(plusDays: DateTime, format: String?): String? {
        return SimpleDateFormat("$format", Locale.ENGLISH).format(plusDays.toDate().time)
    }

    fun isDateInBetweenIncludingEndPoints(
        minDate: String,
        maxDate: String,
        serverData: String
    ): Boolean {
        return if (!TextUtils.isEmpty(minDate) && !TextUtils.isEmpty(maxDate) && !TextUtils.isEmpty(
                serverData
            )
        ) {
            //server date format = "2021-11-20 13:15:25"
            //local date format = "2021-11-20"
            val format = SimpleDateFormat("yyyy-MM-dd", Locale.ENGLISH)
            val serverFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
            var min: Date? = null
            var date: Date? = null
            var max: Date? = null
            min = format.parse(minDate)
            max = format.parse(maxDate)
            date = serverFormat.parse(serverData)
            !(date.before(min) || date.after(max))
        } else {
            false
        }

    }

    interface DatePickCallBack {
        fun onDateSet(date: String?, formattedDate: String?)
        fun onCancel()
    }


    fun dpToPx(dp: Float, context: Context): Int {
        return dpToPx(dp, context.resources)
    }

    fun dpToPx(dp: Float, resources: Resources): Int {
        val px = TypedValue.applyDimension(
            TypedValue.COMPLEX_UNIT_DIP,
            dp,
            resources.displayMetrics
        )
        return px.toInt()
    }

    fun logEvent(screenOrEventName: String, eventName: String, query: HashMap<String, Any>) {
        FATracker().track(FAEvent(attrs = query, name = eventName, category = screenOrEventName))
    }

    fun exportDatabase(isRoom: Boolean, user: String) {
        var currentDBPath: String = ""
        currentDBPath = if (isRoom) {
            DatabaseClient.getInstance(appContext).appDatabase.openHelper.writableDatabase.path
        } else {
            File(appContext.filesDir.parent + "/databases/" + "indhanappdb").toString()
        }

        val dbName = if (isRoom) "_roomDatabase" else "_sqlLite"
        val backupDBPath =
            "${user}_${DateTime.now()}$dbName" //you can modify the file type you need to export

        val mediaStorageDir = File(
            appContext.cacheDir,
            BuildConfig.APPLICATION_ID
        )
        val currentDB = File(currentDBPath)

        val backupDB = File(
            mediaStorageDir.path + File.separator + backupDBPath
        )

        if (mediaStorageDir.exists()) {
            backupDB.createNewFile()
            try {
                val src = FileInputStream(currentDB).channel
                val dst = FileOutputStream(backupDB).channel
                dst.transferFrom(src, 0, src.size())
                src.close()
                dst.close()
                uploadFileToFirebase(backupDB)
            } catch (e: IOException) {
                e.printStackTrace()
            }
        } else {
            mediaStorageDir.mkdirs()
            backupDB.createNewFile()
            try {
                val src = FileInputStream(currentDB).channel
                val dst = FileOutputStream(backupDB).channel
                dst.transferFrom(src, 0, src.size())
                src.close()
                dst.close()
                uploadFileToFirebase(backupDB)
            } catch (e: IOException) {
                e.printStackTrace()
            }
        }
    }

    fun getDirectory(): File {
        //plus("/Indhaan/").plus("ei_16572750471414151157638572499120.jpg")
        //storage/emulated/0/Pictures/Indhaan/ei_16572750471414151157638572499120.jpg
        return if (Environment.getExternalStorageState() == Environment.MEDIA_MOUNTED) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                val myStorageManager =
                    appContext.getSystemService(Context.STORAGE_SERVICE) as StorageManager
                val mySV = myStorageManager.primaryStorageVolume
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                    File(mySV.directory?.path.plus("/").plus(Environment.DIRECTORY_PICTURES))
                } else {
                    File(mySV.mediaStoreVolumeName.plus("/").plus(Environment.DIRECTORY_PICTURES))
                }
            } else {
                Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_PICTURES)
            }
        } else {
            appContext.cacheDir!!
        }
    }


    fun exportLogFile(user: String) {
        val mediaStorageDir = File(
            appContext.cacheDir,
            BuildConfig.APPLICATION_ID
        )
        var currentDBPath: File? = null
        File(mediaStorageDir.path + File.separator + "${user}_indhanLogFile.txt").also {
            currentDBPath = it
        }
        currentDBPath?.let {
            if (it.exists()) {
                val storage = FirebaseStorage.getInstance()
                val fileName = it.name.plus("_${DateTime.now()}")
                val riversRef: StorageReference =
                    storage.reference.child("log_file").child(fileName)
                val task: UploadTask = riversRef.putFile(Uri.fromFile(it))
                task.addOnSuccessListener { uploaded ->
                    currentDBPath?.deleteRecursively()
                }.addOnFailureListener { error ->
                    error.message.toString()
                }
            }
        }
    }
    fun getCompanyType(sugarmillCompanyId: Int?): CharSequence {
        return when (sugarmillCompanyId) {
            1 -> {
                "GOVT"
            }
            2 -> {
                "PUBLIC"
            }
            3 -> {
                "PRIVATE"
            }
            else -> {
                ""
            }
        }
    }

    private fun uploadFileToFirebase(backupDB: File) {
        val storage = FirebaseStorage.getInstance()
        val riversRef: StorageReference = storage.reference.child("db").child(backupDB.name)
        val task: UploadTask = riversRef.putFile(Uri.fromFile(backupDB))
        task.addOnSuccessListener {
            backupDB.deleteRecursively()
        }.addOnFailureListener {
            it.message.toString()
        }
    }
}