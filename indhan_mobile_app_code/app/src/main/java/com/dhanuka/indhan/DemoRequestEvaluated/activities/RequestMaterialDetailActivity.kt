package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.View
import android.widget.TextView
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.TimeLineViewAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponseData
import com.dhanuka.indhan.DemoRequestEvaluated.models.TimeLineModel
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.google.gson.Gson
import kotlinx.android.synthetic.main.requested_material_detail.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList

class RequestMaterialDetailActivity : BaseActivity(), TimeLineViewAdapter.ReceiveMaterialClick {
    private var selectedDate: String = ""
    private var requestedData: RequestedMaterialResponseData? = null
    private var adapter: TimeLineViewAdapter? = null
    private var timeLineList: ArrayList<TimeLineModel> = ArrayList()
    private lateinit var preference: Preference
    private var userType = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgChat -> {
                val bundle = Bundle()
                bundle.putString("requested_data", Gson().toJson(requestedData))
                launchIntent(ChatActivityForRequestedMaterial::class.java, bundle, false)
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.requested_material_detail)
        preference = Preference(this)
        userType = preference.getString("user_type")
        buildTimeLineData()
    }

    private fun buildTimeLineData() {
        imgChat.setOnClickListener(this)
        requestedData = Gson().fromJson(
            intent?.getStringExtra("requested_data").toString(),
            RequestedMaterialResponseData::class.java
        )
        timeLineList.clear()

        requestedData?.let {
            val timeLineFirstString =
                "Crop Name\n\nProduct Name\n\nRequested Quantity\n\nEstimated Demo\n\nGodown\n\nNeeded By"
            val timeLineSecondString =
                " - ${it.crop_name}\n\n - ${it.product_name}\n\n - ${it.requested_units} Kg/Ltr\n\n - ${it.expected_demos}\n\n - ${it.godown_name}\n\n - ${it.need_by_date}"
            val timeLineData = TimeLineModel(
                data = timeLineFirstString,
                dataValue = timeLineSecondString,
                time = it.created_at,
                status = 0,
                comingStatus = 1,
                type = "Requested Details"
            )
            timeLineList.add(timeLineData)

            if (userType.equals("SALES_REGIONAL", ignoreCase = true)) {
                var rsmApprovalText = ""
                var rsmApprovalValueText = ""
                rsmApprovalText = "Status"
                rsmApprovalValueText = when (it.rsm_approval_status) {
                    0 -> {
                        " - Pending"
                    }
                    1 -> {
                        " - Approved"
                    }
                    else -> {
                        " - Rejected"
                    }
                }
                if ((it.rsm_approval_status ?: 0) > 0) {
                    var rsmApprovalText2 = ""
                    var rsmApprovalValueText2 = ""
                    rsmApprovalText2 = "Status"
                    rsmApprovalValueText2 = when (it.rsm_approval_status) {
                        0 -> {
                            " - Pending"
                        }
                        1 -> {
                            " - Approved"
                        }
                        else -> {
                            " - Rejected"
                        }
                    }
                    val timeLineRsm = TimeLineModel(
                        data = rsmApprovalText2,
                        dataValue = rsmApprovalValueText2,
                        time = it.rsm_approval_date,
                        status = 1,
                        comingStatus = it.rsm_approval_status,
                        type = "RSM Approval Status"
                    )
                    timeLineList.add(timeLineRsm)
                } else {
                    val timeLineRsm = TimeLineModel(
                        data = rsmApprovalText,
                        dataValue = rsmApprovalValueText,
                        time = it.rsm_approval_date,
                        status = 1,
                        comingStatus = it.rsm_approval_status,
                        approveQty = it.rsm_approved_unit,
                        type = "Approval Status"
                    )
                    timeLineList.add(timeLineRsm)
                }
            } else {
                var rsmApprovalText = ""
                var rsmApprovalValueText = ""
                rsmApprovalValueText = when (it.rsm_approval_status) {
                    0 -> {
                        " - Pending"
                    }
                    1 -> {
                        " - Approved"
                    }
                    else -> {
                        " - Rejected"
                    }
                }

                rsmApprovalText = "Status"
                val timeLineRsm = TimeLineModel(
                    data = rsmApprovalText,
                    dataValue = rsmApprovalValueText,
                    time = it.rsm_approval_date,
                    status = 1,
                    comingStatus = it.rsm_approval_status,
                    type = "RSM Approval Status"
                )
                timeLineList.add(timeLineRsm)
            }
            if (it.rsm_approval_status != 0) {
                var adminApprovalText = ""
                var adminApprovalValueText = ""
                adminApprovalText = "Status"
                adminApprovalValueText = when (it.admin_approval_status) {
                    0 -> {
                        " - Pending"
                    }
                    1 -> {
                        " - Approved"
                    }
                    else -> {
                        " - Rejected"
                    }
                }
                val timeLineAdmin = TimeLineModel(
                    data = adminApprovalText,
                    dataValue = adminApprovalValueText,
                    time = it.admin_approval_date,
                    status = 2,
                    comingStatus = it.admin_approval_status,
                    type = "Admin Approval Status"
                )
                timeLineList.add(timeLineAdmin)

                if (it.admin_approval_status != 0) {
                    var dispatchText = ""
                    var dispatchValueText = ""
                    dispatchText = if (it.dispatch_status == 0) {
                        "Status"
                    } else {
                        "ET Dispatch\n\nDispatch Status\n\nDispatched Units\n\nActual Dispatch Date"
                    }

                    dispatchValueText = if (it.dispatch_status == 0) {
                        " - Yet To Dispatch"
                    } else {
                        " - ${it.et_dispatch_date}\n\n - Dispatched\n\n - ${it.dispatch_units}Kg/Ltr\n\n - ${it.actual_dispatch_date}"
                    }
                    val time =
                        if (it.dispatch_status == 0) it.et_dispatch_date else it.actual_dispatch_date
                    val timeLineDispatch = TimeLineModel(
                        data = dispatchText,
                        dataValue = dispatchValueText,
                        time = time,
                        status = 3,
                        comingStatus = it.dispatch_status,
                        type = "Dispatch Details"
                    )
                    timeLineList.add(timeLineDispatch)
                    var changeView = false
                    if (userType.equals("area", ignoreCase = true) || userType.equals("sales_area", ignoreCase = true)) {
                        changeView = true
                    }
                    if (it.dispatch_status != 0) {
                        var markReceiving = ""
                        var markReceivingValue = ""
                        markReceiving = if (it.receiving_status == 0 && changeView) {
                            "changeView"
                        } else {
                            "Received Units"
                        }
                        markReceivingValue = if (it.receiving_status == 0 && changeView) {
                            " - 0"
                        } else {
                            " - ${it.received_unit}"
                        }
                        val receivingTime =
                            if (it.receiving_status == 0) it.updated_at else it.receiving_status_captured_date
                        val timeLineMarkReceiving = TimeLineModel(
                            data = markReceiving,
                            dataValue = markReceivingValue,
                            time = receivingTime,
                            status = 4,
                            comingStatus = it.receiving_status,
                            receivedQty = it.received_unit,
                            type = if(changeView) "Mark Receiving" else "Receiving"
                        )
                        timeLineList.add(timeLineMarkReceiving)
                    }
                }
            }
            setAdapter()
        }
    }

    private fun setAdapter() {
        adapter = TimeLineViewAdapter(timeLineList, this@RequestMaterialDetailActivity)
        rvRequestedMaterialRecycler.adapter = adapter
        adapter?.setClickListener(this)
    }

    override fun onClickReceiveMaterial(data: TimeLineModel, trim: String) {
        if (CheckConnectionStatus.isInternetAvailable(this@RequestMaterialDetailActivity)) {
            showProgress()
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["demo_material_request_id"] = requestedData?.demo_material_request_id ?: 0
            hashMap["receiving_status"] = "1"
            hashMap["received_comment"] = "Received"
            hashMap["received_unit"] = trim
            hashMap["receiving_date"] = selectedDate.plus(" 00:00:00")
            val dateString: String =
                SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH).format(Date().time)
            hashMap["receiving_status_captured_date"] = dateString

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .updateReceivingMaterial(hashMap).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CommonAtpResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable?) {
                        hideProgress()
                    }

                    override fun onNext(it: CommonAtpResponse?) {
                        hideProgress()
                        it?.let {
                            if (it.status_code == 200) {
                                makeToast("Receiving status updated successfully.")
                                finish()
                            } else if (it.status_code == 409) {
                                EventBus.getDefault().post("logout")
                            }
                        }
                    }

                })
        } else {
            hideProgress()
            makeToast("Internet connection not available.")
        }
    }

    override fun onClickApproveRejectMaterial(data: TimeLineModel, trim: String, type: Int) {
        if (CheckConnectionStatus.isInternetAvailable(this@RequestMaterialDetailActivity)) {
            showProgress()
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["demo_material_request_id"] = requestedData?.demo_material_request_id ?: 0
            hashMap["receiving_status"] = "1"
            hashMap["rsm_approval_status"] = type
            hashMap["rsm_approval_comment"] = if (type == 1) "Approved" else "Rejected"
            hashMap["rsm_approved_unit"] = trim
            val dateString: String =
                SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH).format(Date().time)
            hashMap["rsm_approval_date"] = dateString

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .markApproveOrReject(hashMap).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CommonAtpResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable?) {
                        hideProgress()
                    }

                    override fun onNext(it: CommonAtpResponse?) {
                        hideProgress()
                        it?.let {
                            if (it.status_code == 200) {
                                makeToast("Status updated successfully.")
                                finish()
                            } else if (it.status_code == 409) {
                                EventBus.getDefault().post("logout")
                            }
                        }
                    }
                })
        } else {
            hideProgress()
            makeToast("Internet connection not available.")
        }
    }

    override fun onClickDate(data: TimeLineModel, view: TextView, count: String, pos: Int) {
        showDatePicker(view, pos, count)
    }

    private fun showDatePicker(textView: TextView, pos: Int, number: String) {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)

        val mDatePicker = DatePickerDialog(
            activity,
            { view, year, monthOfYear, dayOfMonth ->
                selectedDate = "" + year + "-" + (monthOfYear + 1) + "-" + dayOfMonth
                textView.text = selectedDate
                timeLineList[pos].selectedReceiveDate = selectedDate
                timeLineList[pos].receivedQty = number
                adapter?.notifyItemChanged(pos)
            },
            year,
            month,
            day
        )
        mDatePicker.datePicker.maxDate = System.currentTimeMillis()
        // Subtract 6 days from Calendar current date
        c.add(Calendar.DAY_OF_WEEK, -6)
        // Set the Calendar new date as minimum date of date picker
        mDatePicker.datePicker.minDate = c.timeInMillis
        mDatePicker.show()
    }

}