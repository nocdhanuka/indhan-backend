package com.dhanuka.indhan.TestimonialList.filter

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew
import com.dhanuka.indhan.DemoRequestActivity.model.ProductlistNew
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_testimonial_filter.*
import kotlinx.android.synthetic.main.activity_testimonial_list.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import java.util.Arrays.asList


class TestimonialFilterActivity : BaseActivity() {
    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.llFL -> {
                if (!flSpinnerDialog.isEnable)
                    flSpinnerDialog.showSpinerDialog()
            }
            R.id.llRSM -> {
                if (!rsmNameDialog.isEnable)
                    rsmNameDialog.showSpinerDialog()
            }
            R.id.llASM -> {
                if (!asmNameDialog.isEnable)
                    asmNameDialog.showSpinerDialog()
            }
            R.id.llDD -> {
                if (!ddSpinnerDialog.isEnable)
                    ddSpinnerDialog.showSpinerDialog()
            }
        }
    }

    private lateinit var testimonialSpinner: NumberSpinner
    private lateinit var productSpinner: ProductListDialog

    private lateinit var preference: Preference
    private var productList = mutableListOf<ProductlistNew>()
    private var ddList = mutableListOf<DDBean.Data>()
    private var ratingList = mutableListOf<STARBean>()
    private lateinit var adapter: DDListAdapter
    private lateinit var adapterStar: StarListAdapter

    private var testimonial = ""
    private var product = ""
    private var productId = ""
    private var startDate = ""
    private var endDate = ""
    private var rating = ""

    private var list = mutableListOf<String>()

    var fl = ""
    var asmId = ""
    var rsmId = ""

    var flName = ""
    var asmName = ""
    var rsmName = ""

    var type = ""
    var ID = ""

    var csv = ""

    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var rsmNameDialog: RSMNameDialog
    private lateinit var flSpinnerDialog: FLNameDialog
    private lateinit var ddSpinnerDialog: DDFilterNameDialog

    private var flList: ArrayList<ACPFIlterBean.Data.BDO> = java.util.ArrayList()
    private var asmList: ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()
    private var ddFilterList: ArrayList<ACPFIlterBean.Data.DD> = java.util.ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_testimonial_filter)
        preference = Preference(this)

        preference = Preference(this)
        if (!preference.getString("user_type").equals("BDO", false)) {
            if (preference.getString("user_type").equals("SALES_REGIONAL")) {
//            llRSM.visibility=View.VISIBLE
                ID = preference.getString("user_id")
                type = "sales_regional"
                llASM.visibility = View.VISIBLE
                llFL.visibility = View.VISIBLE
                llDD.visibility = View.GONE
                llRSM.visibility = View.GONE
                getFilterList(preference.getString("token"))
            } else if (preference.getString("user_type").equals("SALES_AREA")) {
                ID = preference.getString("user_id")
                type = "sales_area"
                llFL.visibility = View.VISIBLE
                llDD.visibility = View.GONE
                llASM.visibility = View.GONE
                llRSM.visibility = View.GONE
                getFilterList(preference.getString("token"))
            } else if (preference.getString("user_type").equals("dhanuka_doctor", false)) {
                llRSM.visibility = View.GONE
                llASM.visibility = View.GONE
                llFL.visibility = View.GONE
                llDD.visibility = View.GONE
            } else {
                llRSM.visibility = View.VISIBLE
                llASM.visibility = View.VISIBLE
                llFL.visibility = View.VISIBLE
                llDD.visibility = View.GONE
                getFilterList(preference.getString("token"))
            }
        } else {
            llASM.visibility = View.GONE
            llRSM.visibility = View.GONE
            llFL.visibility = View.GONE
            llFL.visibility = View.GONE
        }

        intent?.let {

            if (it.hasExtra("rating")) {
                rating = it.getStringExtra("rating") ?: ""
            }

            if (it.hasExtra("startDate")) {
                startDate = it.getStringExtra("startDate") ?: ""
            }

            if (it.hasExtra("endDate")) {
                endDate = it.getStringExtra("endDate") ?: ""
            }

            if (it.hasExtra("product")) {
                product = it.getStringExtra("product") ?: ""
            }

            if (it.hasExtra("productId")) {
                productId = it.getStringExtra("productId") ?: ""
            }

            if (it.hasExtra("testimonial")) {
                testimonial = it.getStringExtra("testimonial") ?: ""
            }

            if (it.hasExtra("dd")) {
                csv = it.getStringExtra("dd") ?: ""
            }

            if (it.hasExtra("fl")) {
                fl = it.getStringExtra("fl") ?: ""
            }

            if (it.hasExtra("rsm")) {
                rsmId = it.getStringExtra("rsm") ?: ""
            }

            if (it.hasExtra("fasml")) {
                asmId = it.getStringExtra("asm") ?: ""
            }

            if (it.hasExtra("flName")) {
                flName = it.getStringExtra("flName") ?: ""
                txtFL.text = flName
            }

            if (it.hasExtra("rsmName")) {
                rsmName = it.getStringExtra("rsmName") ?: ""
                txtRSM.text = rsmName
            }

            if (it.hasExtra("asmName")) {
                asmName = it.getStringExtra("asmName") ?: ""
                txtASM.text = asmName
            }
        }


        /*get product list and DD list*/

        showProgress()

        addData("5")
        addData("4")
        addData("3")
        addData("2")
        addData("1")
        addData("0")

        getProductList()

        getDDList()

        setView()

        setRatingAdapter()

        setPreviousData()

        spinner_testimonial.setOnClickListener {
            if (!testimonialSpinner.isEnable)
                testimonialSpinner.showSpinerDialog()
        }

        fromDate.setOnClickListener {
            Utility.getPickedDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {

                }

                override fun onDateSet(date: String?, formattedDate: String?) {
                    fromDate.text = date
                    startDate = date!!
                }
            })

        }

        toDate.setOnClickListener {
            Utility.getPickedDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {

                }

                override fun onDateSet(date: String?, formattedDate: String?) {
                    toDate.text = date
                    endDate = date!!
                }
            })

        }

        spinnerProduct.setOnClickListener {
            if (!productSpinner.isEnable)
                productSpinner.showSpinerDialog()
        }

        btnSubmit.setOnClickListener {
            rating = ""
            for (i in ddList.indices) {
                list.add(ddList[i].ddId)
            }
            val idList: String = list.toString()

            val csv = idList.substring(1, idList.length - 1).replace(", ", ",")

            ratingList.forEach {
                if (it.isSelected) {
                    rating = rating + it.rating + ","
                }
            }

            val i = Intent(this@TestimonialFilterActivity, callingActivity!!::class.java)
            i.putExtra("startDate", startDate)
            i.putExtra("endDate", endDate)
            i.putExtra("product", product)
            i.putExtra("productId", productId)
            i.putExtra("testimonial", testimonial)

            i.putExtra("dd", csv)

            i.putExtra("rsm", rsmId)
            i.putExtra("rsmName", rsmName)

            i.putExtra("asm", asmId)
            i.putExtra("asmName", asmName)

            i.putExtra("fl", fl)
            i.putExtra("flName", flName)


            i.putExtra("rating", removeLastChar(rating))

            setResult(100, i)
            finish()


        }

        btnClear.setOnClickListener {
            testimonial = ""
            product = ""
            productId = ""
            startDate = ""
            endDate = ""
            rating = ""

            fl = ""
            flName = ""

            rsmId = ""
            rsmName = ""

            asmId = ""
            asmName = ""

            fromDate.text = ""
            toDate.text = ""
            spinner_testimonial.text = ""
            spinnerProduct.text = ""

            txtASM.text = ""
            txtRSM.text = ""
            txtFL.text = ""

            ratingList.clear()
            addData("5")
            addData("4")
            addData("3")
            addData("2")
            addData("1")
            addData("0")

            setRatingAdapter()
        }
    }

    private fun setPreviousData() {
        if (rating.isNotEmpty()) {
            val result: MutableList<Array<String>>? =
                asList(rating.split(("\\s*,\\s*").toRegex()).dropLastWhile { it.isEmpty() }
                    .toTypedArray())
            ratingList.forEach {
                for (i in 0 until result!![0].size) {
                    if (it.rating.equals(result[0][i])) {
                        it.isSelected = true
                    }
                }
            }
        }

        if (startDate.isNotEmpty()) {
            fromDate.text = startDate
        }

        if (endDate.isNotEmpty()) {
            toDate.text = endDate
        }

        if (testimonial.isNotEmpty()) {
            spinner_testimonial.text = testimonial
        }

        if (product.isNotEmpty()) {
            spinnerProduct.text = product
        }

        if (flName.isNotEmpty()) {
            txtFL.text = flName
        }

        if (rsmName.isNotEmpty()) {
            txtRSM.text = rsmName
        }

        if (asmName.isNotEmpty()) {
            txtASM.text = asmName
        }
        adapterStar.notifyDataSetChanged()

    }

    private fun removeLastChar(str: String?): String? {
        var str = str
        if (str != null && str.isNotEmpty()) {
            str = str.substring(0, str.length - 1)
        }
        return str
    }

    private fun setRatingAdapter() {
        adapterStar = StarListAdapter(this, ratingList)
        rvTestimonialStar.layoutManager =
            LinearLayoutManager(this@TestimonialFilterActivity)
        rvTestimonialStar.adapter = adapterStar
    }

    /*
    * set view
    * */
    private fun setView() {

        llRSM.setOnClickListener(this)
        llASM.setOnClickListener(this)
        llFL.setOnClickListener(this)
        llDD.setOnClickListener(this)

        testimonialSpinner =
            NumberSpinner(this, Utility.dataListofTestimonial, "Select Testimonial")

        testimonialSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (item.equals("Others")) {
                    //spinner_testimonial.visibility = View.VISIBLE
//                    isOthers = true
                    testimonial = item
                    spinner_testimonial.text = item
                } else {
                    //spinner_testimonial.visibility = View.GONE
//                    isOthers = false
                    spinner_testimonial.text = item
                    testimonial = item
                }
            }
        })

        productSpinner =
            ProductListDialog(this@TestimonialFilterActivity, productList, "Select Product")

        productSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct.text = item
                product = productList[position].product
                productId = productList[position].product_id
            }
        })

        flSpinnerDialog = FLNameDialog(this, flList, "Select FL", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                flName = flList[position].name
                txtFL.text = flName
                fl = flList[position].id
                ID = sId
                type = "bdo"

                ddList.clear()
                txtDD.text = ""
                asmId = ""
                //DDID = ""
                getFilterList(preference.getString("token"))
            }

        })

        asmNameDialog = ASMNameDialog(this, asmList, "Select ASM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                asmName = item
                txtASM.text = asmName
                asmId = sId
                ID = sId
                type = "sales_area"
                flList.clear()
                ddList.clear()
                txtFL.text = ""
                txtDD.text = ""
                asmId = ""
                fl = ""
                getFilterList(preference.getString("token"))

            }

        })

        rsmNameDialog = RSMNameDialog(this, rsmList, "Select RSM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                rsmName = item
                txtRSM.text = rsmName
                rsmId = sId
                ID = sId
                type = "sales_regional"
                asmList.clear()
                flList.clear()
                ddList.clear()
                txtASM.text = ""
                txtFL.text = ""
                txtDD.text = ""
                asmId = ""
                fl = ""
                getFilterList(preference.getString("token"))


            }

        })

        ddSpinnerDialog = DDFilterNameDialog(this, ddFilterList, "Select DD", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {

            }
        })

    }

    /*get product list */
    private fun getProductList() {

        var model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getProductListNew(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductListResponseNew>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ProductListResponseNew) {
                    hideProgress()

                    if (listBean.status_code == 200) {
                        productList.addAll(listBean.data.productlist)
                        productSpinner.updatelist(productList)


                    } else {

                    }
                }
            })

    }

    /*
    * get DD list
    * */
    private fun getDDList() {

        var model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getDDList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DDBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: DDBean) {
                    hideProgress()

                    if (listBean.statusCode == 200) {
                        ddList.clear()
                        ddList.add(
                            DDBean.Data(
                                false,
                                "",
                                "",
                                0.0,
                                0,
                                "",
                                "",
                                "",
                                "",
                                "",
                                "0",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "My Self",
                                "",
                                "",
                                0
                            )
                        )
                        ddList.addAll(listBean.data)

                        adapter = DDListAdapter(
                            this@TestimonialFilterActivity,
                            ddList,
                            object : ViewClickListener {
                                override fun onUnChecked(position: Int) {

                                }

                                override fun onClick(position: Int) {

                                }
                            })
                        recyclerView.layoutManager =
                            LinearLayoutManager(this@TestimonialFilterActivity)
                        recyclerView.adapter = adapter
                    }
                }
            })

    }

    /*getDePartment list */

    /**
     * call all list of supervisor, FL,DD
     * */
    private fun getFilterList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["type"] = type
        query["apiToken"] = token
        query["id"] = ID


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
                        rsmList.addAll(villageBean.data.rSMList)
                        asmList.addAll(villageBean.data.aSMList)
                        flList.addAll(villageBean.data.bDOList)
                        ddFilterList.addAll(villageBean.data.dDList)

                    }


                }
            }
            )
    }

    private fun addData(rating: String) {
        val filterDataModel = STARBean(false, rating)
        ratingList.add(filterDataModel)
    }
}
