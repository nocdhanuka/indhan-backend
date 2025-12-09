package com.dhanuka.indhan.institutional.activity.sugarMill

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.activity.TestimonialsListActivity
import com.dhanuka.indhan.institutional.adapter.sugarMill.SugarMillBranchListAdapter
import com.dhanuka.indhan.institutional.adapter.sugarMill.SugarMillViewContactAdapter
import com.dhanuka.indhan.institutional.model.sugarMill.ContactListData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.Currency
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_view_sugarmill_detail.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class SugarMillDetailActivity : BaseActivity(), SugarMillViewContactAdapter.OnClickItem,
    SugarMillBranchListAdapter.OnClickViewBranchItem {
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var arrayListOfData: ArrayList<ContactListData> = ArrayList()
    private var arrayListOfBranchData: ArrayList<SugarMillListData> = ArrayList()
    private var addContactAdapter: SugarMillViewContactAdapter? = null
    private var branchListAdapter: SugarMillBranchListAdapter? = null
    private var sugarMill: SugarMillListData? = null
    private var parentId: String = ""
    private var sugarMillType: String = ""
    private var userType: String = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnVisitHistory -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", sugarMillType)
                bundle.putString("for", "visit_history")
                launchIntent(SugarMillAtpListActivity::class.java, bundle, true)
            }
            R.id.btnViewTestimonial -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", "2")
                launchIntent(TestimonialsListActivity::class.java, bundle, true)
            }
            R.id.btnAddMoreContact -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", sugarMillType)
                launchIntent(AddMoreContactSugarMillActivity::class.java, bundle, true)
            }
            R.id.btnAddNewBranch -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", sugarMillType)
                bundle.putString("branch", "yes")
                launchIntent(AddSugarMillBranchActivity::class.java, bundle, true)
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_view_sugarmill_detail)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        userType = preference?.getString("user_type") ?: ""
        intent?.getStringExtra("sugarmill_data")?.let {
            sugarMill = Gson().fromJson(it, SugarMillListData::class.java)
            parentId = (sugarMill?.sugarmill_id ?: "").toString()
            sugarMill?.contact_data?.let { contactList ->
                contactList.forEach() { contact ->
                    arrayListOfData.add(contact)
                }
            }

            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    arrayListOfBranchData = (appDatabase?.sugarMillListDao()
                        ?.allSugarMillsBranchByOfficeTypeAndSugarMillType(
                            sugarMill?.sugarmill_id ?: 0, "2",
                        ) ?: ArrayList<SugarMillListData>()) as ArrayList<SugarMillListData>
                }.run {
                    withContext(Dispatchers.Main) {
                        setAdapter()
                        setData()
                    }
                }
            }
        }

        intent?.getStringExtra("from_tea").let {
            if (it != null) {
                relBranchDetail.visibility = View.VISIBLE
                btnAddNewBranch.visibility = View.VISIBLE
            } else {
                relBranchDetail.visibility = View.GONE
                btnAddNewBranch.visibility = View.GONE
            }
        }

        btnVisitHistory.setOnClickListener(this)
        btnAddNewBranch.setOnClickListener(this)
        btnAddMoreContact.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        btnViewTestimonial.setOnClickListener(this)
    }

    private fun setData() {
        Glide.with(this)
            .asBitmap()
            .load(Constants.IMAGEBASEURL + sugarMill?.image)
            .into(ivCompanyImage)

        officeType.text =
            "SugarMill - ".plus(if (sugarMill?.office_type == "1") "Head Office" else "Branch Office")
        tvManagerName.text = "Assigned To - ".plus(sugarMill?.fl_name)
        tvFlName.visibility = View.GONE
        if (sugarMill?.office_type == "1") {
            llType.visibility = View.VISIBLE
            llTurnOverWords.visibility = View.VISIBLE
            llDistrict.visibility = View.VISIBLE
        } else {
            llType.visibility = View.GONE
            llTurnOverWords.visibility = View.GONE
            llDistrict.visibility = View.GONE
        }
        tvCompanyName.text = sugarMill?.name
        tvCategoryData.text = sugarMill?.company_category
        tvTurnover.text = sugarMill?.turn_over
        if (!TextUtils.isEmpty(sugarMill?.turn_over)) {
            "In Words - ${
                Currency.convertToIndianCurrency(
                    sugarMill?.turn_over.toString().trim()
                )
            }".also { tvInWords.text = it }
        }
        tvLandArea.text = sugarMill?.land_area
        tvCompanyType.text = getCompanyType(sugarMill?.sugarmill_company_id)
        tvAddress.text = sugarMill?.address
    }

    private fun getCompanyType(sugarmillCompanyId: Int?): CharSequence {
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

    private fun setAdapter() {
        //Contact Adapter
        addContactAdapter =
            SugarMillViewContactAdapter(this@SugarMillDetailActivity, arrayListOfData)
        rcvContact.adapter = addContactAdapter
        addContactAdapter?.setClickListener(this)

        //Branch Adapter
        branchListAdapter =
            SugarMillBranchListAdapter(this@SugarMillDetailActivity, arrayListOfBranchData)
        rcvBranch.adapter = branchListAdapter
        branchListAdapter?.setViewClickListener(this)
    }

    override fun onClickView(view: View, subMenu: ContactListData) {
        //contact
    }

    override fun onClickView(view: View, subMenu: SugarMillListData) {
        //branch
        val bundle = Bundle()
        bundle.putString("sugarmill_data", Gson().toJson(subMenu))
        launchIntent(SugarMillDetailActivity::class.java, bundle, true)
    }
}