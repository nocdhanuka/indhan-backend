package com.dhanuka.indhan.acpfarmers

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import android.widget.TextView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestActivity.models.AllVillageListResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnGetId
import kotlinx.android.synthetic.main.activity_acp_farmer_filter.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class AcpFarmerFilterActivity : BaseActivity() {
    private lateinit var villagelSpinnerDialog: AllVillageSpinnerDialog
    private lateinit var rsmSpinnerDialog: RSMNameDialog
    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var flNameDialog: FLNameDialog
    private lateinit var ddSpinnerDialog: DDFilterNameDialog

    var rsmList: ArrayList<ACPFIlterBean.Data.RSM> = ArrayList()
    var asmList: ArrayList<ACPFIlterBean.Data.ASM> = ArrayList()
    var flList: ArrayList<ACPFIlterBean.Data.BDO> = ArrayList()
    private var ddListFilter: ArrayList<ACPFIlterBean.Data.DD> = ArrayList()

    private lateinit var preference: Preference
    var arrayList: ArrayList<String> = ArrayList()
    private var ddList = mutableListOf<ACPFarmerBean.Data.DDList>()
    private lateinit var ddNameDialog: DdNameDialog
    private lateinit var cropSpinnerDialog: CropSpinnerDialogNew
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    var selectedDate = ""
    var villageList: ArrayList<AllVillageListResponse.Data> = ArrayList()
    var apiToken: String = ""
    var villageId: String = ""
    var rsmId: String = ""
    var asmId: String = ""
    var flId: String = ""

    var sums: Double = 0.0
    var ddName: String = ""
    var ddId: String = ""
    var fromDate = ""
    var toDate = ""

    private val crop1RecordResponseModel = ArrayList<CropBean>()
    var cropId = ""
    var acr = ""
    var cropName = ""
    private var type = ""
    private var ID = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_acp_farmer_filter)
        preference = Preference(this)
        apiToken = preference.getString("token")
        //get crop list
        updateCropList()
        //get village list
        getVillageList(apiToken)
        //show acres
        showPurchasedAcreArea()
        //apply click on view
        applyClickOnView()
        if (intent.hasExtra("ddlist")) {
            ddList = intent.getSerializableExtra("ddlist") as MutableList<ACPFarmerBean.Data.DDList>
        }

        if (preference.getString("user_type").equals("BDO", false)) {
            llASM.visibility = View.GONE
            llRSM.visibility = View.GONE
            llFL.visibility = View.GONE
            llDD.visibility = View.GONE
        } else {
            btnClear.visibility = View.GONE
            when {
                preference.getString("user_type").equals("SALES_REGIONAL") -> {
                    llRSM.visibility = View.GONE
                    ID = preference.getString("user_id")
                    type = "sales_regional"
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("SALES_AREA") -> {
                    ID = preference.getString("user_id")
                    type = "sales_area"
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("dhanuka_doctor", false) -> {
                    llRSM.visibility = View.GONE
                    llASM.visibility = View.GONE
                    llFL.visibility = View.GONE
                    llDD.visibility = View.GONE
                }
                else -> {
                    llRSM.visibility = View.VISIBLE
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    getFilterList(preference.getString("token"))
                }
            }
            getFilterList(preference.getString("token"))
            llVillage.visibility = View.GONE
            llDd.visibility = View.GONE

        }


    }

    /**
     * apply click on view
     */
    private fun applyClickOnView() {
        iCross.setOnClickListener(this)
        llCrop.setOnClickListener(this)
        llAcerage.setOnClickListener(this)
        llDd.setOnClickListener(this)
        llVillage.setOnClickListener(this)
        llFrom.setOnClickListener(this)
        llTo.setOnClickListener(this)
        btnSubmit.setOnClickListener(this)
        llASM.setOnClickListener(this)
        llRSM.setOnClickListener(this)
        llFL.setOnClickListener(this)
        llDD.setOnClickListener(this)
        btnClear.setOnClickListener(this)

    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnClear -> {
                txtCrop.text = ""
                txtAcerage.text = ""
                txtFrom.text = ""
                txtTo.text = ""
                txtRSM.text = ""
                txtASM.text = ""
                txtFL.text = ""
                txtDD.text = ""
                txtDd.text = ""
                txtVillage.text = ""
            }
            R.id.iCross -> {
                this@AcpFarmerFilterActivity.finish()
            }
            R.id.llCrop -> {
                cropSpinnerDialog = CropSpinnerDialogNew(
                    this,
                    crop1RecordResponseModel,
                    "Select or Search Crop",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, cropID: String) {
                            txtCrop.text = item
                            cropId = cropID
                            cropName = item
                        }
                    }
                )
                cropSpinnerDialog.showSpinerDialog()
            }
            R.id.llAcerage -> {
                showListPopUpWindow()
            }

            R.id.llDd -> {
                if (ddList.size > 0) {
                    ddNameDialog =
                        DdNameDialog(this, ddList, "Select or Search DD Name", object : OnGetId {
                            override fun onClick(item: String, position: Int, id: String) {
                                txtDd.text = item
                                ddId = id
                                ddName = item
                            }

                        })
                    ddNameDialog.showSpinerDialog()
                }
            }
            R.id.llVillage -> {
                if (villageList.size > 0) {
                    villagelSpinnerDialog = AllVillageSpinnerDialog(
                        this,
                        villageList,
                        "Select or Search Village",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                villageId = sId
                                txtVillage.text = item

                            }

                        })
                    villagelSpinnerDialog.showSpinerDialog()
                }
            }
            R.id.llFrom -> {
                showDate(txtFrom)

            }
            R.id.llTo -> {
                showDate(txtTo)
            }
            R.id.llASM -> {
                if (asmList.size > 0) {
                    asmNameDialog =
                        ASMNameDialog(this, asmList, "Select or Search ASM ", object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {

                                txtASM.text = item
                                asmId = sId
                                ID = sId
                                type = "sales_area"
                                getFilterList(preference.getString("token"))


                            }

                        })
                    asmNameDialog.showSpinerDialog()
                }

            }

            R.id.llRSM -> {

                if (rsmList.size > 0) {
                    rsmSpinnerDialog =
                        RSMNameDialog(this, rsmList, "Select or Search ASM ", object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                txtRSM.text = item
                                rsmId = sId
                                ID = sId
                                type = "sales_regional"
                                getFilterList(preference.getString("token"))


                            }

                        })
                    rsmSpinnerDialog.showSpinerDialog()
                }

            }
            R.id.llFL -> {

                if (flList.size > 0) {
                    flNameDialog =
                        FLNameDialog(this, flList, "Select or Search FL ", object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                txtFL.text = item
                                flId = sId
                                ID = sId
                                type = "bdo"
                                getFilterList(preference.getString("token"))


                            }

                        })
                    flNameDialog.showSpinerDialog()
                }
            }
            R.id.llDD -> {

                if (ddListFilter.size > 0) {
                    ddSpinnerDialog = DDFilterNameDialog(
                        this,
                        ddListFilter,
                        "Select or Search DD ",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                txtDD.text = item
                                ddId = sId
                            }

                        })
                    ddSpinnerDialog.showSpinerDialog()
                }
            }

            R.id.btnSubmit -> {
                acr = txtAcerage.text.toString()
                fromDate = txtFrom.text.toString()
                toDate = txtTo.text.toString()
                if (preference.getString("user_type").equals("BDO", false)) {
                    val intent = Intent()
                    intent.putExtra("cropid", cropId)
                    intent.putExtra("acr", acr)
                    intent.putExtra("ddid", ddId)
                    intent.putExtra("villageid", villageId)
                    intent.putExtra("fromdate", fromDate)
                    intent.putExtra("todate", toDate)
                    intent.putExtra("rsmId", rsmId)
                    intent.putExtra("asmId", asmId)
                    intent.putExtra("flId", flId)
                    setResult(Activity.RESULT_OK, intent)
                    this@AcpFarmerFilterActivity.finish()
                } else {
                    if (TextUtils.isEmpty(cropId) &&
                        TextUtils.isEmpty(acr) &&
                        TextUtils.isEmpty(ddId) &&
                        TextUtils.isEmpty(villageId) &&
                        TextUtils.isEmpty(fromDate) &&
                        TextUtils.isEmpty(toDate) &&
                        TextUtils.isEmpty(rsmId) &&
                        TextUtils.isEmpty(asmId) &&
                        TextUtils.isEmpty(flId)
                    ) {
                        makeToast("Please select dd.")
                    } else {
                        val intent = Intent()
                        intent.putExtra("cropid", cropId)
                        intent.putExtra("acr", acr)
                        intent.putExtra("ddid", ddId)
                        intent.putExtra("villageid", villageId)
                        intent.putExtra("fromdate", fromDate)
                        intent.putExtra("todate", toDate)
                        intent.putExtra("rsmId", rsmId)
                        intent.putExtra("asmId", asmId)
                        intent.putExtra("flId", flId)
                        setResult(Activity.RESULT_OK, intent)
                        this@AcpFarmerFilterActivity.finish()
                    }
                }
            }
        }
    }

    /**
     * Get crop list
     */
    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        var prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        if (prpdCropListNew != null) {
            prpdCropListNew.forEach {
                var bean = CropBean()
                bean.setcID(it.cropId.toInt())
                bean.item = it.crop
                if (!checkValue(bean.getcID()!!))
                    crop1RecordResponseModel.add(bean)
            }
        }
    }

    private fun checkValue(id: Int): Boolean {
        crop1RecordResponseModel.forEach {
            if (id == it.getcID()) {
                return true
            }
        }
        return false
    }

    /*
show date
*/
    private fun showDate(txtFromDate: TextView): Unit {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)
        var m = ""
        var d = ""

        val mDatePicker = DatePickerDialog(
            activity,
            { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                //lblDate.setText("" + dayOfMonth + " " + MONTHS[monthOfYear] + ", " + year)

                if ((monthOfYear + 1) < 10) {
                    m = "0" + (monthOfYear + 1)
                } else {
                    m = "" + (monthOfYear + 1)
                }
                if (dayOfMonth < 10) {
                    d = "0" + dayOfMonth
                } else {
                    d = "" + dayOfMonth
                }

                selectedDate = "$year-$m-$d"
                txtFromDate.text = selectedDate

            },
            year,
            month,
            day
        )
        mDatePicker.datePicker.maxDate = System.currentTimeMillis()
        // Subtract 3 months from Calendar updated date
        c.add(Calendar.MONTH, -12)
        // Set the Calendar new date as minimum date of date picker
        mDatePicker.datePicker.minDate = c.timeInMillis;
        mDatePicker.show()
    }

    //get crop villageList
    private fun getVillageList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        // query.put("ECN", ecn)
        query.put("apiToken", token)
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getDdVillageList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AllVillageListResponse>() {
                override fun onCompleted() {
                    hideProgress()

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: AllVillageListResponse) {
                    hideProgress()
                    if (!villageBean.data.isNullOrEmpty()) {
                        villageList.clear()
                        villageList.addAll(villageBean.data)
                    }

                }
            }
            )
    }

    //end
    private fun showPurchasedAcreArea() {
        for (items in 0..99) {
            sums = sums + 0.5
            arrayList.add("" + sums + " Acre")
        }
    }

    private fun showListPopUpWindow() {
        val adapter = ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, arrayList)
        var listPopUpWindow: ListPopupWindow = ListPopupWindow(this)
        listPopUpWindow.setOnItemClickListener(AdapterView.OnItemClickListener { parent, view, position, id ->
            txtAcerage.text = arrayList.get(position)
            listPopUpWindow.dismiss()
        })
        listPopUpWindow.anchorView = llAcerage
        listPopUpWindow.setAdapter(adapter)
        listPopUpWindow.show()
    }


    private fun getFilterList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        // query.put("ECN", ecn)
        query["type"] = type
        query["id"] = ID
        query["apiToken"] = token
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).departmentlist(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ACPFIlterBean>() {
                override fun onCompleted() {
                    hideProgress()

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: ACPFIlterBean) {
                    hideProgress()
                    if (villageBean.statusCode == 200) {
                        if (villageBean.data.rSMList.isNotEmpty()) {
                            rsmList.clear()
                        }
                        if (villageBean.data.aSMList.isNotEmpty()) {
                            asmList.clear()
                        }
                        if (villageBean.data.bDOList.isNotEmpty()) {
                            flList.clear()
                        }

                        rsmList.addAll(villageBean.data.rSMList)
                        asmList.addAll(villageBean.data.aSMList)
                        flList.addAll(villageBean.data.bDOList)
                        ddListFilter.addAll(villageBean.data.dDList)

                    }


                }
            }
            )
    }

}