package com.dhanuka.indhan.institutional.activity

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import android.view.inputmethod.InputMethodManager
import android.widget.TextView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllDistrictBean
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean
import com.dhanuka.indhan.Model.ArrivalDepartureResponse
import com.dhanuka.indhan.Model.LanguageModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.AllMultipleNumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.LanguageSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.MultipleModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.activity.influencers.InfluencersTestimonialListActivity
import com.dhanuka.indhan.institutional.adapter.AddTestimonialDataAdapter
import com.dhanuka.indhan.institutional.model.*
import com.dhanuka.indhan.institutional.model.influencers.InfluencerTestimonialData
import com.dhanuka.indhan.institutional.model.influencers.InfluencersTestimonials
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.OnSpinnerMultipleItemClick
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_add_circulars.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import org.joda.time.format.DateTimeFormat
import org.joda.time.format.DateTimeFormatter
import org.json.JSONArray
import org.json.JSONObject
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class AddCircularsActivity : BaseActivity(), AddTestimonialDataAdapter.OnClickItem {
    private var type: String = ""
    private var arrayListOfData: LinkedList<AddTestimonialsModel> = LinkedList()
    private var addTestimonialAdapter: AddTestimonialDataAdapter? = null
    private var contactStr: String = ""
    private var contactId: String = ""
    private var selectedDate: String = ""
    private var appDatabase: AppDatabase? = null
    private var preference: Preference? = null
    private var listOfData: ArrayList<TestimonialListData> = ArrayList()
    private var contactListData: ArrayList<ContactTeaEstate> = ArrayList()
    private var listOfDataTestimonialInfluencer: ArrayList<InfluencerTestimonialData> = ArrayList()
    private var influencerContactListData: ArrayList<ContactInfluencers> = ArrayList()
    private var sugarMillContactListData: ArrayList<ContactSugarMill> = ArrayList()
    private var contactIdList: ArrayList<String> = ArrayList()
    private var selectedContactList: ArrayList<String> = ArrayList()
    private val contactDropDownList = ArrayList<MultipleModel>()
    private var selectContact: AllMultipleNumberSpinner? = null
    private var circularData: CircularListData? = null
    private var selectInstitutionalType: NumberSpinner? = null
    private var languageSpinnerDialog: LanguageSpinner? = null
    private var languageList = ArrayList<LanguageModel>()
    private var languageName = ""
    private var languageId = 0
    private var stateSpinner: NumberSpinner? = null
    private var districtSpinner: NumberSpinner? = null
    private var stateModelLis: ArrayList<AllStateBean.StateData> = ArrayList()
    private var districtList: ArrayList<AllDistrictBean.DistrictData> = ArrayList()
    private var state = ""
    private var state_id = "0"
    private var district_id = "0"
    private var district = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_circulars)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setListeners()
        getDropDownListType()
        updateLanguageList()
        intent?.getStringExtra("circular_data")?.let {
            circularData = Gson().fromJson(it, CircularListData::class.java)
            tvTitle.text = "Edit Circular"
            setCircularDataEdit()
        }
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtPersonName -> {
                selectContact?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog(contactIdList)
                    }
                }
            }
            R.id.spinnerInstitutionalType -> {
                selectInstitutionalType?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.tvSelectPublishedDate -> {
                showDate(tvSelectPublishedDate)
            }
            R.id.ivBack -> {
                finish()
            }
            R.id.spinnerLanguage -> {
                languageSpinnerDialog?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtSubmitDistributor -> {
                val newRecordContact: ArrayList<AddTestimonialsModel> = ArrayList()
                for (indexOfArray in arrayListOfData.indices) {
                    if (valid(indexOfArray)) {
                        newRecordContact.add(arrayListOfData[indexOfArray])
                    } else {
                        break
                    }
                }
                if (newRecordContact.size == 0) {
                    makeToast("There is no testimonial added, please add new farmers.")
                } else if (newRecordContact.size != 0) {
                    callApiAddCircular(newRecordContact)
                }
            }
            R.id.txtState -> {
                hideKeyboardFrom(this, txtState)
                stateSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtDistrict -> {
                hideKeyboardFrom(this, txtDistrict)
                districtSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtAddMore -> {
                var someIndexIsEmpty = false
                for (position in arrayListOfData.indices) {
                    if (TextUtils.isEmpty(arrayListOfData[position].testimonialMessage.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter Message for testimonial.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].testimonialId)) {
                        someIndexIsEmpty = true
                        makeToast("Please select testimonial")
                        break
                    }
                }
                if (!someIndexIsEmpty) {
                    arrayListOfData.add(
                        arrayListOfData.size,
                        createEmptyAddContactModel()
                    )
                    addContactItem()
                }
            }
        }
    }

    private fun callApiAddCircular(newRecordContact: ArrayList<AddTestimonialsModel>) {
        val array = JSONArray()
        newRecordContact.forEach() {
            val jsonObject = JSONObject()
            jsonObject.accumulate("testimonial_id", it.testimonialId)
            jsonObject.accumulate("text", it.testimonialMessage)
            array.put(jsonObject)
        }
        val query = HashMap<String, Any>()
        query["apiToken"] = preference?.getString("token") ?: ""
        query["circular_name"] = etCircularName.text.toString().trim()
        query["published_date"] = selectedDate.plus(" 00:00:00")
        query["language_id"] = languageId
        query["language_name"] = languageName
        query["contact_ids"] = contactIdList.toString().replace("[", "").replace("]", "")
        query["whatsapp_message"] = txtAddProperitor.text.toString().trim()
        query["circular_data"] = array.toString()
        query["institutionalType"] = type
        query["state_id"] = state_id
        query["district_id"] = district_id
        query["state"] = state
        query["district"] = district
        circularData?.let {
            query["circular_id"] = it.circular_id
        }
        callApi(query)
    }

    private fun callApi(query: HashMap<String, Any>) {
        showProgress()
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .createCircular(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ArrivalDepartureResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable?) {
                    hideProgress()
                }

                override fun onNext(it: ArrivalDepartureResponse?) {
                    hideProgress()
                    if (it != null) {
                        if (it.status_code == 200) {
                            makeToast(
                                (it.message
                                    ?: "Circular added Successfully!").plus(", Please refresh this screen.")
                            )
                            EventBus.getDefault()
                                .post(RefreshEventModel(data = 3, page = "circular"))
                            val intent = Intent()
                            setResult(RESULT_OK, intent)
                            finish()
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            makeToast(it.message)
                        }
                    }
                }
            })
    }

    private fun valid(indexOfArray: Int): Boolean {
        val subMenu = arrayListOfData[indexOfArray]
        return if (!TextUtils.isEmpty(subMenu.testimonialMessage)) {
            if (!TextUtils.isEmpty(subMenu.testimonialId)) {
                true
            } else {
                makeToast("Please select testimonial")
                false
            }
        } else {
            makeToast("Please enter Message for testimonial.")
            false
        }
    }

    private fun updateLanguageList() {
        languageList.clear()
        languageList = DatabaseManager.getLanguageList(this)
        languageList.let {
            languageSpinnerDialog?.updateList(it)
        }
    }

    private fun getDropDownListType() {
        val typeStringList = ArrayList<String>()
        typeStringList.add("Tea Estate")
        typeStringList.add("Sugar Mill")
        typeStringList.add("Influencer")
        selectInstitutionalType = NumberSpinner(this, typeStringList, "Select Institution Type")
        selectInstitutionalType?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerInstitutionalType.text = item
                when (item) {
                    "Tea Estate" -> {
                        if (type != "1") {
                            state = ""
                            state_id = "0"
                            district = ""
                            district_id = "0"
                            selectedContactList.clear()
                            arrayListOfData.clear()
                        }
                        type = "1"
                        if (CheckConnectionStatus.isInternetAvailable(this@AddCircularsActivity)) {
                            getContacts("1")
                        } else {
                            getLocalData("1")
                        }
                    }
                    "Sugar Mill" -> {
                        if (type != "2") {
                            state = ""
                            state_id = "0"
                            district = ""
                            district_id = "0"
                            selectedContactList.clear()
                            arrayListOfData.clear()
                        }
                        type = "2"
                        if (CheckConnectionStatus.isInternetAvailable(this@AddCircularsActivity)) {
                            getContacts("2")
                        } else {
                            getLocalData("2")
                        }
                    }
                    "Influencer" -> {
                        if (type != "3") {
                            state = ""
                            state_id = "0"
                            district = ""
                            district_id = "0"
                            selectedContactList.clear()
                            arrayListOfData.clear()
                        }
                        type = "3"
                        if (CheckConnectionStatus.isInternetAvailable(this@AddCircularsActivity)) {
                            getContacts("3")
                        } else {
                            getLocalData("3")
                        }
                    }
                }
            }
        })

        languageSpinnerDialog = LanguageSpinner(this, languageList, "Select or Search Language")
        languageSpinnerDialog?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                languageId = languageList[position].id.toInt()
                languageName = languageList[position].language
                spinnerLanguage.text = languageList[position].language
            }
        })
    }

    private fun setCircularDataEdit() {
        arrayListOfData.clear()
        type = circularData?.institutionalType ?: "0"
        getContacts(type)
        circularData?.circular_data?.forEach {
            arrayListOfData.add(
                AddTestimonialsModel(
                    id = System.currentTimeMillis(),
                    testimonialMessage = it.description ?: "",
                    testimonialLink = "",
                    testimonialId = (it.estate_testimonial_id ?: 0).toString(),
                    testimonialName = it.testimonial_name ?: ""
                )
            )
        }
        spinnerInstitutionalType.text = getInstitutionType(type)
        spinnerLanguage.text = circularData?.language_name
        languageId = (circularData?.language_id ?: "0").toInt()
        languageName = circularData?.language_name ?: ""

        circularData?.circular_contacts?.forEach() {
            contactStr = it.name!!
            contactId = it.tea_estate_contact_id.toString()
            contactIdList.add(contactId)
            selectedContactList.add(contactStr)
            txtPersonName.text =
                selectedContactList.toString().replace("[", "").replace("]", "")
        }
        txtState.text = circularData?.state ?: ""
        txtDistrict.text = circularData?.district ?: ""
        district_id = circularData?.district_id ?: "0"
        state_id = circularData?.state_id ?: "0"
        district = circularData?.district ?: ""
        state = circularData?.state ?: ""
        etCircularName.setText(circularData?.circular_name ?: "")
        val dateTime = circularData?.published_date
        // Format for input
        val dtf: DateTimeFormatter = DateTimeFormat.forPattern("yyyy-MM-dd HH:mm:ss")
        // Parsing the date
        val jodatime: DateTime = dtf.parseDateTime(dateTime)
        // Format for output
        val dtfOut: DateTimeFormatter = DateTimeFormat.forPattern("yyyy-MM-dd")
        // Printing the date
        selectedDate = dtfOut.print(jodatime)
        tvSelectPublishedDate.text = selectedDate

        txtAddProperitor.setText(circularData?.whatsapp_message ?: "")

        selectContact = AllMultipleNumberSpinner(this, contactDropDownList, "Select Contacts")
        val listeners = object : OnSpinnerMultipleItemClick {
            override fun onClick(data: ArrayList<MultipleModel>) {
                contactIdList.clear()
                selectedContactList.clear()
                data.forEach {
                    contactStr = it.name
                    contactId = it.id
                    contactIdList.add(contactId)
                    selectedContactList.add(contactStr)
                    txtPersonName.text =
                        selectedContactList.toString().replace("[", "").replace("]", "")
                }
            }
        }
        selectContact?.bindOnSpinerListener(listeners)
    }

    private fun getInstitutionType(type: String): String {
        return when (type) {
            "1" -> {
                "Tea Estate"
            }
            "2" -> {
                "Sugar Mill"
            }
            "3" -> {
                "Influencer"
            }
            else -> {
                "-"
            }
        }
    }

    private fun setListeners() {
        tvSelectPublishedDate.setOnClickListener(this)
        txtPersonName.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        txtSubmitDistributor.setOnClickListener(this)
        spinnerLanguage.setOnClickListener(this)
        txtAddMore.setOnClickListener(this)
        txtState.setOnClickListener(this)
        txtDistrict.setOnClickListener(this)
        spinnerInstitutionalType.setOnClickListener(this)
    }

    private fun showDate(txtFromDate: TextView) {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)

        val mDatePicker = DatePickerDialog(
            activity,
            { _, years, monthOfYear, dayOfMonth ->
                val months: String = if ((monthOfYear + 1).toString().length == 1) {
                    "0${monthOfYear + 1}"
                } else {
                    "${monthOfYear + 1}"
                }
                val days: String = if (dayOfMonth.toString().length == 1) {
                    "0$dayOfMonth"
                } else {
                    "$dayOfMonth"
                }
                selectedDate = "$years-$months-$days"
                txtFromDate.text = selectedDate
            },
            year,
            month,
            day
        )
        mDatePicker.datePicker.minDate = System.currentTimeMillis()
        c.add(Calendar.MONTH, 6)
        mDatePicker.datePicker.maxDate = c.timeInMillis
        mDatePicker.show()
    }

    private fun getTestimonials() {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .getTestimonialList("0")
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TestimonialListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable?) {
                    hideProgress()
                }

                override fun onNext(it: TestimonialListResponse?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            insertTestimonialDataInDb(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    private fun insertTestimonialDataInDb(data: List<TestimonialListData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.testimonialListDataDao()?.insert(teaEstate)
                    }
                }
                "v3/testimonialListInstitutional".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData(type)
                    hideProgress()
                }
            }
        }
    }

    private fun getContacts(type: String) {
        when (type) {
            "1" -> {
                showProgress()
                val query = HashMap<String, Any>()
                query["updated_date"] = "0"
                RetrofitClient.mvpClient
                    .create(ApiServiceRequest::class.java)
                    .getAllContactsTeaEstate(query)
                    .subscribeOn(Schedulers.newThread())
                    .observeOn(AndroidSchedulers.mainThread())
                    .subscribe(object : Subscriber<ContactListResponse>() {
                        override fun onCompleted() {
                            hideProgress()
                        }

                        override fun onError(e: Throwable?) {
                            hideProgress()
                        }

                        override fun onNext(it: ContactListResponse?) {
                            hideProgress()
                            if (it != null) {
                                if (it.status_code == 200) {
                                    insertContactDataInDb(it.data)
                                } else if (it.status_code == 409) {
                                    EventBus.getDefault().post("logout")
                                }
                            }
                        }
                    })
            }
            "2" -> {
                showProgress()
                val query = HashMap<String, Any>()
                query["updated_date"] = "0"
                RetrofitClient.mvpClient
                    .create(ApiServiceRequest::class.java)
                    .getAllContactsSugarMill(query)
                    .subscribeOn(Schedulers.newThread())
                    .observeOn(AndroidSchedulers.mainThread())
                    .subscribe(object : Subscriber<SugarMillContactListResponse>() {
                        override fun onCompleted() {
                            hideProgress()
                        }

                        override fun onError(e: Throwable?) {
                            hideProgress()
                        }

                        override fun onNext(it: SugarMillContactListResponse?) {
                            hideProgress()
                            if (it != null) {
                                if (it.status_code == 200) {
                                    insertSugarMillContactDataInDb(it.data)
                                } else if (it.status_code == 409) {
                                    EventBus.getDefault().post("logout")
                                }
                            }
                        }
                    })
            }
            "3" -> {
                showProgress()
                val query = HashMap<String, Any>()
                query["updated_date"] = "0"
                RetrofitClient.mvpClient
                    .create(ApiServiceRequest::class.java)
                    .getAllContactsInfluencers(query)
                    .subscribeOn(Schedulers.newThread())
                    .observeOn(AndroidSchedulers.mainThread())
                    .subscribe(object : Subscriber<InfluencersContactListResponse>() {
                        override fun onCompleted() {
                            hideProgress()
                        }

                        override fun onError(e: Throwable?) {
                            hideProgress()
                        }

                        override fun onNext(it: InfluencersContactListResponse?) {
                            hideProgress()
                            if (it != null) {
                                if (it.status_code == 200) {
                                    insertInfluencersContactDataInDb(it.data)
                                } else if (it.status_code == 409) {
                                    EventBus.getDefault().post("logout")
                                }
                            }
                        }
                    })
            }
        }

    }

    private fun hideKeyboardFrom(context: Context, view: View) {
        val imm: InputMethodManager =
            context.getSystemService(Activity.INPUT_METHOD_SERVICE) as InputMethodManager
        imm.hideSoftInputFromWindow(view.windowToken, 0)
    }

    private fun insertInfluencersContactDataInDb(data: List<ContactInfluencers>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.contactInfluecersDao()?.insert(teaEstate)
                    }
                }
                "v3/getAllContactsInfleuncers".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getTestimonialsInfluencers()
                }
            }
        }
    }

    private fun getTestimonialsInfluencers() {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .getTestimonialInfluencersList("0")
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<InfluencersTestimonials>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable?) {
                    hideProgress()
                }

                override fun onNext(it: InfluencersTestimonials?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            insertTestimonialDataInfluencerInDb(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            })
    }

    private fun insertTestimonialDataInfluencerInDb(data: List<InfluencerTestimonialData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach() {
                    appDatabase?.influencersTestimonialsDataListDao()?.insert(it)
                }
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData(type)
                    hideProgress()
                }
            }
        }
    }

    private fun insertSugarMillContactDataInDb(data: List<ContactSugarMill>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.contactSugarMillDao()?.insert(teaEstate)
                    }
                }
                "v3/getAllContactsSugarmill".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getTestimonials()
                }
            }
        }
    }

    private fun insertContactDataInDb(data: List<ContactTeaEstate>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.contactTeaEstateDao()?.insert(teaEstate)
                    }
                }
                "v3/getAllContacts".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getTestimonials()
                }
            }
        }
    }

    private fun getLocalData(type: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                when (type) {
                    "1" -> {
                        listOfData = (appDatabase?.testimonialListDataDao()
                            ?.allTestimonialSharedData("1", "1")
                            ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                        contactListData = (appDatabase?.contactTeaEstateDao()
                            ?.getAllContactListData()
                            ?: ArrayList<ContactTeaEstate>()) as ArrayList<ContactTeaEstate>
                    }
                    "2" -> {
                        listOfData = (appDatabase?.testimonialListDataDao()
                            ?.allTestimonialSharedData("1", "2")
                            ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                        sugarMillContactListData = (appDatabase?.contactSugarMillDao()
                            ?.getAllContactListData()
                            ?: ArrayList<ContactSugarMill>()) as ArrayList<ContactSugarMill>
                    }
                    "3" -> {
                        listOfDataTestimonialInfluencer =
                            (appDatabase?.influencersTestimonialsDataListDao()
                                ?.allTestimonialData("Farmer")
                                ?: ArrayList<InfluencerTestimonialData>()) as ArrayList<InfluencerTestimonialData>
                        influencerContactListData = (appDatabase?.contactInfluecersDao()
                            ?.getAllContactListData()
                            ?: ArrayList<ContactInfluencers>()) as ArrayList<ContactInfluencers>
                    }
                }
                stateModelLis.clear()
                val state = appDatabase?.stateDao()?.allStateList
                state?.let { stateModelLis.addAll(it) }
            }.run {
                delay(300)
                withContext(Dispatchers.Main) {
                    setDropDownData()
                    setAdapter()
                    addContactItem()
                    txtAddMore.visibility = View.VISIBLE
                }
            }
        }
    }

    private fun setDropDownData() {
        if (circularData == null) {
            contactIdList.clear()
        }
        selectedContactList.clear()
        contactDropDownList.clear()
        val allModel = MultipleModel()
        allModel.id = System.currentTimeMillis().toString()
        allModel.name = "Select All"
        allModel.selected = false
        contactDropDownList.add(allModel)
        when (type) {
            "1" -> {
                contactListData.forEach() {
                    val model = MultipleModel()
                    model.id = it.tea_estate_contact_id.toString()
                    model.name = it.name
                    model.selected = false
                    if (contactIdList.size > 0) {
                        model.selected = contactIdList.contains(it.tea_estate_contact_id.toString())
                    } else {
                        model.selected = false
                    }
                    contactDropDownList.add(model)
                }
            }
            "2" -> {
                sugarMillContactListData.forEach() {
                    val model = MultipleModel()
                    model.id = it.sugarmill_contact_id.toString()
                    model.name = it.name
                    if (contactIdList.size > 0) {
                        model.selected = contactIdList.contains(it.sugarmill_contact_id.toString())
                    } else {
                        model.selected = false
                    }
                    contactDropDownList.add(model)
                }
            }
            "3" -> {
                influencerContactListData.forEach() {
                    val model = MultipleModel()
                    model.id = it.influencer_contact_id.toString()
                    model.name = it.name
                    if (contactIdList.size > 0) {
                        model.selected = contactIdList.contains(it.influencer_contact_id.toString())
                    } else {
                        model.selected = false
                    }
                    contactDropDownList.add(model)
                }
            }
        }

        selectContact = AllMultipleNumberSpinner(
            this@AddCircularsActivity,
            contactDropDownList,
            "Select Contacts"
        )
        val listeners = object : OnSpinnerMultipleItemClick {
            override fun onClick(data: ArrayList<MultipleModel>) {
                contactIdList.clear()
                selectedContactList.clear()
                data.forEach {
                    if (!it.name.equals("Select All", ignoreCase = true)) {
                        contactStr = it.name
                        contactId = it.id
                        contactIdList.add(contactId)
                        selectedContactList.add(contactStr)
                        txtPersonName.text =
                            selectedContactList.toString().replace("[", "").replace("]", "")
                    }
                }
            }
        }
        selectContact?.bindOnSpinerListener(listeners)

        val stateListString = ArrayList<String>()
        stateModelLis.forEach() {
            it.state?.let { it1 -> stateListString.add(it1) }
        }
        stateSpinner = NumberSpinner(this, stateListString, "Select State")
        stateSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (stateModelLis.size > 0) {
                    state = item
                    state_id = stateModelLis[position].state_id ?: ""
                    txtState.text = item
                    GlobalScope.launch {
                        val districtListString = ArrayList<String>()
                        withContext(Dispatchers.IO) {
                            val updatedVal =
                                appDatabase?.districtDao()?.getAllDistrictList(state_id.toInt())
                            districtList.clear()
                            updatedVal?.let { districtList.addAll(it) }
                            updatedVal?.forEach() {
                                it.district?.let { it1 -> districtListString.add(it1) }
                            }
                        }.run {
                            withContext(Dispatchers.Main) {
                                setSpinnerDistrict(districtListString)
                                if (circularData == null) {
                                    contactIdList.clear()
                                }
                                selectedContactList.clear()
                                contactDropDownList.clear()
                                val allModel = MultipleModel()
                                allModel.id = System.currentTimeMillis().toString()
                                allModel.name = "Select All"
                                allModel.selected = false
                                contactDropDownList.add(allModel)
                                when (type) {
                                    "1" -> {
                                        contactListData.forEach() {
                                            if (it.state_id == state_id) {
                                                val model = MultipleModel()
                                                model.id = it.tea_estate_contact_id.toString()
                                                model.name = it.name
                                                if (contactIdList.size > 0) {
                                                    model.selected = contactIdList.contains(it.tea_estate_contact_id.toString())
                                                } else {
                                                    model.selected = false
                                                }
                                                contactDropDownList.add(model)
                                            }
                                        }
                                    }
                                    "2" -> {
                                        sugarMillContactListData.forEach() {
                                            if (it.state_id == state_id) {
                                                val model = MultipleModel()
                                                model.id = it.sugarmill_contact_id.toString()
                                                model.name = it.name
                                                if (contactIdList.size > 0) {
                                                    model.selected = contactIdList.contains(it.sugarmill_contact_id.toString())
                                                } else {
                                                    model.selected = false
                                                }
                                                contactDropDownList.add(model)
                                            }
                                        }
                                    }
                                    "3" -> {
                                        influencerContactListData.forEach() {
                                            if (it.state_id == state_id) {
                                                val model = MultipleModel()
                                                model.id = it.influencer_contact_id.toString()
                                                model.name = it.name
                                                if (contactIdList.size > 0) {
                                                    model.selected = contactIdList.contains(it.influencer_contact_id.toString())
                                                } else {
                                                    model.selected = false
                                                }
                                                contactDropDownList.add(model)
                                            }
                                        }
                                    }
                                }

                                selectContact = AllMultipleNumberSpinner(
                                    this@AddCircularsActivity,
                                    contactDropDownList,
                                    "Select Contacts"
                                )
                                val listeners = object : OnSpinnerMultipleItemClick {
                                    override fun onClick(data: ArrayList<MultipleModel>) {
                                        contactIdList.clear()
                                        selectedContactList.clear()
                                        data.forEach {
                                            if (!it.name.equals("Select All", ignoreCase = true)) {
                                                contactStr = it.name
                                                contactId = it.id
                                                contactIdList.add(contactId)
                                                selectedContactList.add(contactStr)
                                                txtPersonName.text =
                                                    selectedContactList.toString().replace("[", "")
                                                        .replace("]", "")
                                            }
                                        }
                                    }
                                }
                                selectContact?.bindOnSpinerListener(listeners)
                            }
                        }
                    }
                }
            }
        })


    }


    private fun setSpinnerDistrict(districtListString: ArrayList<String>) {
        districtSpinner = NumberSpinner(this, districtListString, "Select District")
        districtSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (districtList.size > 0) {
                    district = item
                    district_id = (districtList[position].district_id ?: "").toString()
                    txtDistrict.text = item
                    if (circularData == null) {
                        contactIdList.clear()
                    }
                    selectedContactList.clear()
                    contactDropDownList.clear()
                    val allModel = MultipleModel()
                    allModel.id = System.currentTimeMillis().toString()
                    allModel.name = "Select All"
                    allModel.selected = false
                    contactDropDownList.add(allModel)
                    when (type) {
                        "1" -> {
                            contactListData.forEach() {
                                if ((it.state_id == state_id) && (it.district_id == district_id)) {
                                    val model = MultipleModel()
                                    model.id = it.tea_estate_contact_id.toString()
                                    model.name = it.name
                                    if (contactIdList.size > 0) {
                                        model.selected = contactIdList.contains(it.tea_estate_contact_id.toString())
                                    } else {
                                        model.selected = false
                                    }
                                    contactDropDownList.add(model)
                                }
                            }
                        }
                        "2" -> {
                            sugarMillContactListData.forEach() {
                                if ((it.state_id == state_id) && (it.district_id == district_id)) {
                                    val model = MultipleModel()
                                    model.id = it.sugarmill_contact_id.toString()
                                    model.name = it.name
                                    if (contactIdList.size > 0) {
                                        model.selected = contactIdList.contains(it.sugarmill_contact_id.toString())
                                    } else {
                                        model.selected = false
                                    }
                                    contactDropDownList.add(model)
                                }
                            }
                        }
                        "3" -> {
                            influencerContactListData.forEach() {
                                if ((it.state_id == state_id) && (it.district_id == district_id)) {
                                    val model = MultipleModel()
                                    model.id = it.influencer_contact_id.toString()
                                    model.name = it.name
                                    if (contactIdList.size > 0) {
                                        model.selected = contactIdList.contains(it.influencer_contact_id.toString())
                                    } else {
                                        model.selected = false
                                    }
                                    contactDropDownList.add(model)
                                }
                            }
                        }
                    }

                    selectContact = AllMultipleNumberSpinner(
                        this@AddCircularsActivity,
                        contactDropDownList,
                        "Select Contacts"
                    )
                    val listeners = object : OnSpinnerMultipleItemClick {
                        override fun onClick(data: ArrayList<MultipleModel>) {
                            contactIdList.clear()
                            selectedContactList.clear()
                            data.forEach {
                                if (!it.name.equals("Select All", ignoreCase = true)) {
                                    contactStr = it.name
                                    contactId = it.id
                                    contactIdList.add(contactId)
                                    selectedContactList.add(contactStr)
                                    txtPersonName.text =
                                        selectedContactList.toString().replace("[", "")
                                            .replace("]", "")
                                }
                            }
                        }
                    }
                    selectContact?.bindOnSpinerListener(listeners)
                }
            }
        })
    }

    private fun addContactItem() {
        if (arrayListOfData.size == 0) {
            arrayListOfData.add(0, createEmptyAddContactModel())
        }
        addTestimonialAdapter?.updateListData(listOfData, type, listOfDataTestimonialInfluencer)
        addTestimonialAdapter?.notifyItemChanged(arrayListOfData.size)
    }

    private fun setAdapter() {
        addTestimonialAdapter =
            AddTestimonialDataAdapter(this@AddCircularsActivity, arrayListOfData)
        rcvAddTestimonials.adapter = addTestimonialAdapter
        addTestimonialAdapter?.setClickListener(this)
    }

    private fun createEmptyAddContactModel(): AddTestimonialsModel {
        return AddTestimonialsModel(
            id = System.currentTimeMillis(),
            testimonialMessage = "",
            testimonialLink = "",
            testimonialId = "",
            testimonialName = ""
        )
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = this@updateTimeOnDb.getLastHitTimeData()
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDatabase?.apiTimeDao()?.updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDatabase?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDatabase?.apiTimeDao()?.getApiTimeData(this)
    }

    override fun onClickView(
        view: View,
        subMenu: AddTestimonialsModel,
        position: Int,
        type: String
    ) {
        hideKeyboardFrom(this@AddCircularsActivity, view)
        GlobalScope.launch {
            delay(300)
            withContext(Dispatchers.Main) {
                if (type == "3") {
                    val intent =
                        Intent(
                            this@AddCircularsActivity,
                            InfluencersTestimonialListActivity::class.java
                        )
                    intent.putExtra("position", position)
                    intent.putExtra("from_circular", "true")
                    intent.putExtra("data", Gson().toJson(listOfDataTestimonialInfluencer))
                    startActivityForResult(intent, 21313)
                } else {
                    val intent =
                        Intent(this@AddCircularsActivity, TestimonialsListActivity::class.java)
                    intent.putExtra("position", position)
                    intent.putExtra("from_circular", "true")
                    intent.putExtra("data", Gson().toJson(listOfData))
                    startActivityForResult(intent, 21312)
                }
            }
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == 21312) {
                data?.let {
                    val position = it.getIntExtra("position", 0)
                    val testimonialData =
                        Gson().fromJson(it.getStringExtra("data"), TestimonialListData::class.java)
                    arrayListOfData[position].testimonialId =
                        testimonialData.institutional_testimonial_id.toString()
                    arrayListOfData[position].testimonialLink =
                        testimonialData.image_video.toString()
                    arrayListOfData[position].testimonialName =
                        testimonialData.testimonial_name.toString()
                    addTestimonialAdapter?.notifyItemChanged(position)
                }
            } else if (requestCode == 21313) {
                data?.let {
                    val position = it.getIntExtra("position", 0)
                    val testimonialData =
                        Gson().fromJson(
                            it.getStringExtra("data"),
                            InfluencerTestimonialData::class.java
                        )
                    arrayListOfData[position].testimonialId =
                        testimonialData.testimonial_id
                    arrayListOfData[position].testimonialLink =
                        testimonialData.uploaded_file
                    arrayListOfData[position].testimonialName =
                        testimonialData.farmer
                    addTestimonialAdapter?.notifyItemChanged(position)
                }
            }
        }
    }
}