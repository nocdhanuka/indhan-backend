package com.dhanuka.indhan.institutional.activity

import android.app.ActivityManager
import android.content.Context
import android.content.Intent
import android.media.ThumbnailUtils
import android.os.Build
import android.os.Bundle
import android.provider.MediaStore
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.Model.LanguageModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.LanguageSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.Testimonial.service.TestimonialServiceTeaEstate
import com.dhanuka.indhan.institutional.model.influencers.InfluencersContact
import com.dhanuka.indhan.institutional.model.influencers.InfluencersContactListData
import com.dhanuka.indhan.institutional.model.influencers.InfluencersData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillContact
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillContactListData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillData
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateContact
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateContactListData
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import kotlinx.android.synthetic.main.tea_estate_add_testimonial.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class AddNewTestimonialActivity : BaseActivity() {

    private var dateTime: String = ""
    private var isShare: Boolean = false
    private var contactData: Any? = null
    private var teaEstateId: String = ""
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var videofile: File? = null
    private var selectTeaEstate: NumberSpinner? = null
    private var selectInstitutionalType: NumberSpinner? = null
    private var selectContact: NumberSpinner? = null
    private var languageSpinnerDialog: LanguageSpinner? = null
    private var teaEstateList: ArrayList<TeaEstateStgData> = ArrayList()
    private var influencersList: ArrayList<InfluencersData> = ArrayList()
    private var sugarMillList: ArrayList<SugarMillData> = ArrayList()
    private var contactList: ArrayList<TeaEstateContact> = ArrayList()
    private var influencersContactList: ArrayList<InfluencersContact> = ArrayList()
    private var sugarMillContactList: ArrayList<SugarMillContact> = ArrayList()
    private var languageList = ArrayList<LanguageModel>()
    private var type = ""
    private var institutionalName = ""
    private var officeType = ""
    private var teaType = ""
    private var languageName = ""
    private var languageId = 0
    private var userEcn = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.tea_estate_add_testimonial)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setListeners()
        getLocalData()
        updateLanguageList()
    }

    private fun updateLanguageList() {
        languageList.clear()
        languageList = DatabaseManager.getLanguageList(this)
        languageList.let {
            languageSpinnerDialog?.updateList(it)
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
            viedoPicker.onActivityResult(requestCode, resultCode, data)
        }
    }

    private fun selectVideo() {
        PermissionsUtil.askPermissions(
            this,
            PermissionsUtil.CAMERA,
            PermissionsUtil.STORAGE
        ) { isGranted ->
            if (isGranted) {
                viedoPicker.selectVideo(this, "videoSelect")
            }
        }
    }

    private val viedoPicker by lazy {
        VideopickerUtil(this, object : VideoPickerListener {
            override fun onVideoPicked(path: String, tag: String) {
                videofile = File(path)
                val thumbVideo = ThumbnailUtils.createVideoThumbnail(
                    path,
                    MediaStore.Images.Thumbnails.MICRO_KIND
                )
                layout_img.setImageBitmap(thumbVideo)
                imgView.setImageResource(R.drawable.play)
            }

            override fun onError(msg: String) {
                makeToast("UnSupported Video Please Select Video Having Extension Mp4")
            }
        })
    }

    private fun getLocalData() {
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
                        type = "1"
                        spinnerTeaEstate.hint = "Select Tea Estate / STG"
                        setDropDownDataTea()
                    }
                    "Sugar Mill" -> {
                        type = "2"
                        spinnerTeaEstate.hint = "Select Sugar Mill"
                        setDropDownDataSugar()
                    }
                    "Influencer" -> {
                        type = "3"
                        spinnerTeaEstate.hint = "Select Influencer"
                        setDropDownDataInstitutional()
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

    private fun setDropDownDataInstitutional() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                influencersList.clear()
                (appDatabase?.influencersListDataDao()?.allInfluencers
                    ?: ArrayList<InfluencersData>()).forEach() {
                    influencersList.add(it!!)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    val teaEstateStringList = ArrayList<String>()
                    influencersList.forEach() {
                        it.name?.let { it1 -> teaEstateStringList.add(it1) }
                    }
                    selectTeaEstate = NumberSpinner(
                        this@AddNewTestimonialActivity,
                        teaEstateStringList,
                        "Select Influencer"
                    )
                    selectTeaEstate?.bindOnSpinerListener(object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            spinnerTeaEstate.text = item
                            teaEstateId = influencersList[position].influencer_id.toString()
                            institutionalName = influencersList[position].name.toString()
                            officeType = influencersList[position].office_type.toString()
                            teaType = influencersList[position].type.toString()
                            userEcn = influencersList[position].user_ecn.toString()
                            if (CheckConnectionStatus.isInternetAvailable(this@AddNewTestimonialActivity)) {
                                getDataContactInfluencer(teaEstateId)
                            } else {
                                makeToast("No internet, Please check your internet.")
                            }
                        }
                    })
                }
            }
        }
    }

    private fun getDataContactInfluencer(teaEstateId: String) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getInfluencersContactData(teaEstateId, "0")
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<InfluencersContactListData>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable?) {

                }

                override fun onNext(it: InfluencersContactListData?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            setDataInfluencerContact(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }

            })
    }

    private fun setDataInfluencerContact(data: List<InfluencersContact>?) {
        influencersContactList.clear()
        data?.forEach() {
            influencersContactList.add(it)
        }

        val contactStringList = ArrayList<String>()
        influencersContactList.forEach() {
            it.name.let { it1 -> contactStringList.add(it1) }
        }

        selectContact = NumberSpinner(this, contactStringList, "Select Contact")
        selectContact?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerPerson.text = item
                contactData = influencersContactList[position]
            }
        })
    }

    private fun setDropDownDataSugar() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                sugarMillList.clear()
                (appDatabase?.sugarMillListDataDao()?.allSugarMill
                    ?: ArrayList<SugarMillData>()).forEach() {
                    sugarMillList.add(it!!)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    val teaEstateStringList = ArrayList<String>()
                    sugarMillList.forEach() {
                        it.name?.let { it1 -> teaEstateStringList.add(it1) }
                    }
                    selectTeaEstate = NumberSpinner(
                        this@AddNewTestimonialActivity,
                        teaEstateStringList,
                        "Select Sugar Mill"
                    )
                    selectTeaEstate?.bindOnSpinerListener(object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            spinnerTeaEstate.text = item
                            teaEstateId = sugarMillList[position].sugarmill_id.toString()
                            institutionalName = sugarMillList[position].name.toString()
                            officeType = sugarMillList[position].office_type.toString()
                            teaType = sugarMillList[position].type.toString()
                            userEcn = sugarMillList[position].user_ecn.toString()
                            if (CheckConnectionStatus.isInternetAvailable(this@AddNewTestimonialActivity)) {
                                getDataContactSugarMill(teaEstateId)
                            } else {
                                makeToast("No internet, Please check your internet.")
                            }
                        }
                    })
                }
            }
        }
    }

    private fun getDataContactSugarMill(teaEstateId: String) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getSugarMillContactData(teaEstateId, "0")
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SugarMillContactListData>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable?) {

                }

                override fun onNext(it: SugarMillContactListData?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            setDataSugarMillContact(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }

            })
    }

    private fun setDataSugarMillContact(data: List<SugarMillContact>?) {
        sugarMillContactList.clear()
        data?.forEach() {
            sugarMillContactList.add(it)
        }

        val contactStringList = ArrayList<String>()
        sugarMillContactList.forEach() {
            it.name.let { it1 -> contactStringList.add(it1) }
        }

        selectContact = NumberSpinner(this, contactStringList, "Select Contact")
        selectContact?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerPerson.text = item
                contactData = sugarMillContactList[position]
            }
        })
    }

    private fun setDropDownDataTea() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                teaEstateList.clear()
                (appDatabase?.teaEstateStgListDataDao()?.allTeaEstate
                    ?: ArrayList<TeaEstateStgData>()).forEach() {
                    teaEstateList.add(it!!)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    val teaEstateStringList = ArrayList<String>()
                    teaEstateList.forEach() {
                        it.name?.let { it1 -> teaEstateStringList.add(it1) }
                    }
                    selectTeaEstate = NumberSpinner(
                        this@AddNewTestimonialActivity,
                        teaEstateStringList,
                        "Select Tea Estate / STG"
                    )
                    selectTeaEstate?.bindOnSpinerListener(object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            spinnerTeaEstate.text = item
                            teaEstateId = teaEstateList[position].tea_estate_id.toString()
                            officeType = teaEstateList[position].office_type.toString()
                            teaType = teaEstateList[position].type.toString()
                            userEcn = teaEstateList[position].user_ecn.toString()
                            institutionalName = teaEstateList[position].name.toString()
                            if (CheckConnectionStatus.isInternetAvailable(this@AddNewTestimonialActivity)) {
                                getDataContact(teaEstateId)
                            } else {
                                makeToast("No internet, Please check your internet.")
                            }
                        }
                    })
                }
            }
        }

    }

    private fun getDataContact(teaEstateId: String) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getContactData(teaEstateId, "0")
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TeaEstateContactListData>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable?) {

                }

                override fun onNext(it: TeaEstateContactListData?) {
                    if (it != null) {
                        if (it.status_code == 200) {
                            setDataContact(it.data)
                        } else if (it.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }

            })
    }

    private fun setDataContact(data: List<TeaEstateContact>?) {
        contactList.clear()
        data?.forEach() {
            contactList.add(it)
        }

        val contactStringList = ArrayList<String>()
        contactList.forEach() {
            it.name.let { it1 -> contactStringList.add(it1) }
        }

        selectContact = NumberSpinner(this, contactStringList, "Select Contact")
        selectContact?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerPerson.text = item
                contactData = contactList[position]
            }
        })
    }

    private fun setListeners() {
        btnSubmit.setOnClickListener(this)
        spinnerTeaEstate.setOnClickListener(this)
        spinnerPerson.setOnClickListener(this)
        iCross.setOnClickListener(this)
        spinnerInstitutionalType.setOnClickListener(this)
        rlImgFarmer.setOnClickListener(this)
        txtCaptureImage.setOnClickListener(this)
        imgView.setOnClickListener(this)
        spinnerLanguage.setOnClickListener(this)
        rgbIsShare.setOnCheckedChangeListener { _, checkedId ->
            when (checkedId) {
                R.id.rbShare -> {
                    isShare = true
                }
                R.id.rbNoShare -> {
                    isShare = false
                }
            }
        }
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnSubmit -> {
                if (valid()) {
                    uploadTestimonial()
                }
            }
            R.id.spinnerTeaEstate -> {
                selectTeaEstate?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
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
            R.id.spinnerPerson -> {
                selectContact?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.iCross -> {
                super.onBackPressed()
            }
            R.id.rlImgFarmer -> {
                selectVideo()
            }
            R.id.txtCaptureImage -> {
                selectVideo()
            }
            R.id.imgView -> {
                selectVideo()
            }
            R.id.spinnerLanguage -> {
                languageSpinnerDialog?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
        }
    }

    private fun valid(): Boolean {
        if (TextUtils.isEmpty(etNameTestimonial.text.toString().trim())) {
            makeToast("Please enter testimonial name.")
            return false
        }
        if (TextUtils.isEmpty(spinnerTeaEstate.text.toString().trim())) {
            when (type) {
                "1" -> {
                    makeToast("Please select TeaEstate/STG/Branch.")
                }
                "2" -> {
                    makeToast("Please select SugarMill.")
                }
                "3" -> {
                    makeToast("Please select Influencer.")
                }
            }
            return false
        }
        if (TextUtils.isEmpty(spinnerPerson.text.toString().trim())) {
            makeToast("Please select Contact.")
            return false
        }
        if (TextUtils.isEmpty(etDescription.text.toString().trim())) {
            makeToast("Please enter testimonial description.")
            return false
        }
        if (rgbIsShare.checkedRadioButtonId == -1 || rgbIsShare.checkedRadioButtonId == 0) {
            makeToast("Please select can be share.")
            return false
        }
        if (videofile == null) {
            makeToast("Please capture/select video to add in testimonial.")
            return false
        }
        return true
    }

    private fun uploadTestimonial() {
        val cal = Calendar.getInstance(Locale.ENGLISH)
        val format1 = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
        val formatted = format1.format(cal.time)


        dateTime = formatted
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            if (!isMyServiceRunning(TestimonialServiceTeaEstate::class.java)) {
                val intent = Intent(this, TestimonialServiceTeaEstate::class.java)
                intent.putExtra("image_video", videofile?.absolutePath ?: "")
                intent.putExtra("description", etDescription.text.toString().trim())
                intent.putExtra("tea_estate_id", teaEstateId)
                intent.putExtra("institutional_type", type)
                intent.putExtra("language_id", languageId.toString())
                intent.putExtra("language_name", languageName)
                when (contactData) {
                    is TeaEstateContact -> {
                        intent.putExtra(
                            "tea_contact_id",
                            (contactData as TeaEstateContact).tea_estate_contact_id.toString()
                        )
                        intent.putExtra("contact_name", (contactData as TeaEstateContact).name)
                        intent.putExtra(
                            "contact_designation",
                            (contactData as TeaEstateContact).designation
                        )
                        intent.putExtra(
                            "contact_number",
                            (contactData as TeaEstateContact).mobile_number
                        )
                        intent.putExtra(
                            "wa_contact_number",
                            (contactData as TeaEstateContact).wa_number
                        )
                    }
                    is SugarMillContact -> {
                        intent.putExtra(
                            "tea_contact_id",
                            (contactData as SugarMillContact).sugarmill_contact_id.toString()
                        )
                        intent.putExtra("contact_name", (contactData as SugarMillContact).name)
                        intent.putExtra(
                            "contact_designation",
                            (contactData as SugarMillContact).designation
                        )
                        intent.putExtra(
                            "contact_number",
                            (contactData as SugarMillContact).mobile_number
                        )
                        intent.putExtra(
                            "wa_contact_number",
                            (contactData as SugarMillContact).wa_number
                        )
                    }
                    is InfluencersContact -> {
                        intent.putExtra(
                            "tea_contact_id",
                            (contactData as InfluencersContact).influencer_contact_id.toString()
                        )
                        intent.putExtra("contact_name", (contactData as InfluencersContact).name)
                        intent.putExtra(
                            "contact_designation",
                            (contactData as InfluencersContact).designation
                        )
                        intent.putExtra(
                            "contact_number",
                            (contactData as InfluencersContact).mobile_number
                        )
                        intent.putExtra(
                            "wa_contact_number",
                            (contactData as InfluencersContact).wa_number
                        )
                    }
                }
                intent.putExtra("latitude", preference?.getString(Constants.latitude) ?: "")
                intent.putExtra("longitude", preference?.getString(Constants.longitude) ?: "")
                intent.putExtra("institutional_name", institutionalName)
                intent.putExtra("user_ecn", userEcn)
                intent.putExtra("office_type", officeType)
                intent.putExtra("type", teaType)
                intent.putExtra("date_time", dateTime)
                intent.putExtra("testimonial_name", etNameTestimonial.text.toString().trim())
                intent.putExtra("can_be_shared", if (isShare) "1" else "0")
                startService(intent)
                super.onBackPressed()
            }
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun isMyServiceRunning(serviceClass: Class<*>): Boolean {
        val manager: ActivityManager = getSystemService(Context.ACTIVITY_SERVICE) as ActivityManager
        for (service in manager.getRunningServices(Int.MAX_VALUE)) {
            if (serviceClass.name == service.service.className) {
                return true
            }
        }
        return false
    }

    override fun onBackPressed() {
        super.onBackPressed()
    }

}