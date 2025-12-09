package com.dhanuka.indhan.ACPVisitHistory

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew
import com.dhanuka.indhan.DemoRequestActivity.model.ProductlistNew
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.dhanuka.indhan.validatefarmers.SuccessBean
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean.Data
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerContractor
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerPresenter
import kotlinx.android.synthetic.main.activity_acpvisit_history.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class ACPVisitHistoryActivity : BaseActivity(),
    VisitHistoryFarmerContractor.VisitHistoryFarmerView {
    private var appDatabase: AppDatabase? = null
    private lateinit var mPresenter: VisitHistoryFarmerContractor.VisitHistoryFarmerPresenter
    private lateinit var preference: Preference
    private lateinit var adapter: ACPVisitHistoryListAdapter
    private var productList: ArrayList<ProductlistNew> = ArrayList()
    private var list: MutableList<Data> = ArrayList()

    //    private lateinit var bean: VisitHistoryFarmerBean
    private var mPosition: Int = 0

    override fun setCall(items: SuccessBean) {
        list[mPosition].callStatus = "1"
        adapter.notifyDataSetChanged()
        val intent = Intent(Intent.ACTION_DIAL)
        intent.data = Uri.parse("tel: ${list[mPosition].contact_number}")
        startActivity(intent)
    }

    override fun setChangeStatus(items: SuccessBean) {
        makeToast(items.message)
        list[mPosition].leadStatus = "1"
        adapter.notifyDataSetChanged()
    }

    override fun onClick(v: View?) {

    }

    override fun setItems(items: VisitHistoryFarmerBean) {
        insetDataInDataBase(items.data)
    }

    private fun insetDataInDataBase(data: MutableList<Data>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.forEach() {
                    if(it.callStatus == null){
                        it.callStatus = "";
                    }
                    appDatabase?.visitHistoryDao()?.insert(it)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    "v3/getMarkVisit".updateTimeOnDb()
                    getLocalDbData()
                }
            }
        }
    }

    private fun getLastHitTimeData(apiName: String): ApiTimeTableModel? {
        return appDatabase?.apiTimeDao()?.getApiTimeData(apiName)
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = getLastHitTimeData(this@updateTimeOnDb)
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

    override fun failure(items: String) {
        makeToast(items)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_acpvisit_history)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        mPresenter = VisitHistoryFarmerPresenter(this)
        preference = Preference(this)

        val c = Calendar.getInstance().time
        val df = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedDate: String = df.format(c)

        val c1 = Calendar.getInstance()
        c1.time = DateTime.now().toDate()
        c1.add(Calendar.DATE, -30)
        val nextDate = c1.time
        val formattedFromDate: String = df.format(nextDate)
        getLocalDbData()
        val query = HashMap<String, Any>()
        query["farmerId"] = ""
        query["villageId"] = ""
        query["from_date"] = formattedFromDate
        query["to_date"] = formattedDate
        query["apiToken"] = preference.getString("token")
        var time = "0"
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                time = getLastHitTimeData("v3/getMarkVisit").let { it?.updatedAt ?: "0" }
            }
        }

        query["updated_at"] = time
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val isShowProgress = time == "0"
            mPresenter.onGetVistList(query, isShowProgress)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }

        getProductList()

        imgFilter.setOnClickListener {
            val intent = Intent(this, ACPVisitFilterActivity::class.java)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivityForResult(intent, 100)
        }
    }

    private fun getLocalDbData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                list = (appDatabase?.visitHistoryDao()?.allVisitHistoryList()
                    ?: ArrayList<Data>()) as MutableList<Data>
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun setAdapter() {
        if (list.size > 0) {
            txtNoItem.visibility = View.GONE
            adapter = ACPVisitHistoryListAdapter(this, list, object : ACPListener {
                override fun onCall(position: Int) {
                    mPosition = position
                    val query = HashMap<String, Any>()
                    query["farmerId"] = list[position].farmerId
                    query["status"] = "1"
                    query["farmer_type"] = "normal_farmer"
                    query["apiToken"] = preference.getString("token")
                    if (CheckConnectionStatus.isInternetAvailable(this@ACPVisitHistoryActivity)) {
                        mPresenter.onCallFarmer(query)
                    } else {
                        makeToast(resources.getString(R.string.no_internet_connection))
                    }
                }

                override fun onChanageStatus(position: Int) {
                    mPosition = position
                    showdialog(position)

                }
            })
            recyclerViewVisitHistory.layoutManager =
                LinearLayoutManager(this)
            recyclerViewVisitHistory.adapter = adapter
        } else {
            adapter = ACPVisitHistoryListAdapter(this, ArrayList(), object : ACPListener {
                override fun onCall(position: Int) {
                }

                override fun onChanageStatus(position: Int) {
                }
            })
            recyclerViewVisitHistory.layoutManager =
                LinearLayoutManager(this)
            recyclerViewVisitHistory.adapter = adapter
            txtNoItem.visibility = View.VISIBLE
        }
    }

    private fun showdialog(postion: Int) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_change_status)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
//        msg.text = "Testimonial Successfully Uploaded"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {

            showdialogTwo(postion)
            dialog.dismiss()

        }
        dialog.show()
    }


    private fun showdialogTwo(postion: Int) {
        var dateData: String = ""
        var product: String = ""
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_choose_product)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val txtProduct = dialog.findViewById<View>(R.id.txtProduct) as Medium
        val txtDate = dialog.findViewById<View>(R.id.txtDate) as Medium


        var list = mutableListOf<String>()

        for (i in 0 until productList.size) {
            list.add(productList[i].product)
        }

        txtProduct.setOnClickListener {
            val listPopupAdapter = ArrayAdapter(
                this@ACPVisitHistoryActivity,
                android.R.layout.simple_spinner_dropdown_item,
                list
            )
            val popupWindow = ListPopupWindow(this@ACPVisitHistoryActivity)
            popupWindow.setOnItemClickListener { parent, view, position, id ->
                product = productList[position].product_id
                txtProduct.text = productList[position].product
                popupWindow.dismiss()
            }

            popupWindow.setContentWidth(txtProduct.width)
            popupWindow.height = ListPopupWindow.WRAP_CONTENT
            popupWindow.anchorView = txtProduct
            popupWindow.setAdapter(listPopupAdapter)
            popupWindow.show()
        }


        txtDate.setOnClickListener {
            Utility.getPickedFutureDate(
                this@ACPVisitHistoryActivity,
                object : Utility.DatePickCallBack {
                    override fun onDateSet(date: String?, formattedDate: String?) {
                        txtDate.text = date
                        dateData = date!!
                    }

                    override fun onCancel() {

                    }
                })
        }


//        msg.text = "Testimonial Successfully Uploaded"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeSubmit) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {

            val query = HashMap<String, Any>()
            query["farmerId"] = this.list[postion].farmerId
            query["product_id"] = product
            query["expected_purchase_date"] = dateData
            query["status"] = "1"
            query["farmer_type"] = "normale_farmer"
            query["apiToken"] = preference.getString("token")
            if (CheckConnectionStatus.isInternetAvailable(this@ACPVisitHistoryActivity)) {
                mPresenter.onMakeLead(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
            dialog.dismiss()

        }
        dialog.show()
    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == 100) {

            val query = HashMap<String, Any>()
            query["farmerId"] = ""
            query["villageId"] = data?.getStringExtra("villageId").toString()
            query["from_date"] = data?.getStringExtra("fromDate").toString()
            query["to_date"] = data?.getStringExtra("toDate").toString()
            query["apiToken"] = preference.getString("token")
            query["updated_at"] = "0"
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                mPresenter.onGetVistList(query, true)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        }
    }

    //get crop productListData
    private fun getProductList() {
        val model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getProductListNew(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductListResponseNew>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                }

                override fun onNext(listBean: ProductListResponseNew) {
                    if (listBean.status_code == 200) {
                        productList.clear()
                        productList.addAll(listBean.data.productlist)
                    } else {

                    }
                }
            })

    }
}
