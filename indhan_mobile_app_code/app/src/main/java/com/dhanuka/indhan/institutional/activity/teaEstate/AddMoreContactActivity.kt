package com.dhanuka.indhan.institutional.activity.teaEstate

import android.os.Bundle
import android.text.TextUtils
import android.util.Patterns
import android.view.View
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.teaEstate.AddContactAdapter
import com.dhanuka.indhan.institutional.model.DesignationDataList
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateAddContactModel
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_add_more_contact.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import org.json.JSONArray
import org.json.JSONObject
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class AddMoreContactActivity : BaseActivity(), AddContactAdapter.OnClickItem {

    private var arrayListOfData: LinkedList<TeaEstateAddContactModel> = LinkedList()
    private var addContactAdapter: AddContactAdapter? = null
    private var levelData: ArrayList<String> = ArrayList()
    private var designationData: ArrayList<DesignationDataList> = ArrayList()

    private var preference: Preference? = null
    private var userId = ""
    private var parentId = "0"
    private var appDatabase: AppDatabase? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnRbSubmit -> {
                val newRecordContact: ArrayList<TeaEstateAddContactModel> = ArrayList()
                for (indexOfArray in arrayListOfData.indices) {
                    if (validateFields(indexOfArray)) {
                        newRecordContact.add(arrayListOfData[indexOfArray])
                    } else {
                        break
                    }
                }
                if (newRecordContact.size == 0) {
                    makeToast("There is no contact added, please add contact.")
                } else if (newRecordContact.size != 0) {
                    submitData(newRecordContact)
                }
            }
            R.id.ivBack -> {
                finish()
            }
            R.id.txtAddMore -> {
                var someIndexIsEmpty = false
                for (position in arrayListOfData.indices) {
                    if (TextUtils.isEmpty(arrayListOfData[position].level)) {
                        someIndexIsEmpty = true
                        makeToast("Please select contact level.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].wa_number.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please select contact designation.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].contact_name.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact person name.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].mobile_number.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact mobile number.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].contact_email.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact email address.")
                        break
                    }

                    if (!Patterns.EMAIL_ADDRESS.matcher(arrayListOfData[position].contact_email.trim())
                            .matches()
                    ) {
                        someIndexIsEmpty = true
                        makeToast("Please enter valid contact email address.")
                        break
                    }

                    if (TextUtils.isEmpty(arrayListOfData[position].designation.trim())) {
                        someIndexIsEmpty = true
                        makeToast("Please enter contact email address.")
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

    private fun validateFields(indexOfArray: Int): Boolean {
        val subMenu = arrayListOfData[indexOfArray]
        if (!TextUtils.isEmpty(subMenu.level)) {
            if (!TextUtils.isEmpty(subMenu.wa_number)) {
                if (!TextUtils.isEmpty(subMenu.contact_name)) {
                    if (!TextUtils.isEmpty(subMenu.wa_number)) {
                        return if (!TextUtils.isEmpty(subMenu.mobile_number)) {
                            if (!TextUtils.isEmpty(subMenu.contact_email)) {
                                true
                            } else {
                                makeToast("Please enter contact email address.")
                                false
                            }
                        } else {
                            makeToast("Please enter contact mobile number.")
                            false
                        }
                    } else {
                        makeToast("Please enter contact whatsApp number.")
                        return false
                    }
                } else {
                    makeToast("Please enter contact person name.")
                    return false
                }
            } else {
                makeToast("Please select contact designation.")
                return false
            }
        } else {
            makeToast("Please select contact level.")
            return false
        }
    }

    private fun submitData(newRecordContact: ArrayList<TeaEstateAddContactModel>) {
        val query = HashMap<String, Any>()
        query["apiToken"] = preference?.getString("token") ?: ""

        val array = JSONArray()
        newRecordContact.forEach() {
            val jsonObject = JSONObject()
            jsonObject.accumulate("level", it.level.replace("Level ", ""))
            jsonObject.accumulate("designation", it.designation)
            jsonObject.accumulate("name", it.contact_name)
            jsonObject.accumulate("wa_number", it.wa_number)
            jsonObject.accumulate("mobile_number", it.mobile_number)
            jsonObject.accumulate("email", it.contact_email)
            array.put(jsonObject)
        }

        query["tea_estate_id"] = parentId.toInt()
        query["contact_data"] = array.toString()

        if (CheckConnectionStatus.isInternetAvailable(this@AddMoreContactActivity)) {
            showProgress()
            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).addContacts(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CommonAtpResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable?) {
                        hideProgress()
                        makeToast(e?.message ?: "Please try again later.")
                    }

                    override fun onNext(response: CommonAtpResponse?) {
                        makeToast(response?.message ?: "Contact added successfully.")
                        EventBus.getDefault().post(RefreshEventModel(data = 1, page = "tea_estate"))
                        finish()
                    }
                })
        } else {
            makeToast("No internet connection, Please check.")
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_more_contact)
        preference = Preference(this)
        userId = preference?.getString("user_id") ?: ""
        intent?.getStringExtra("parentId")?.let {
            parentId = it
            val type = intent?.getStringExtra("type") ?: "0"
        }
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        btnRbSubmit.setOnClickListener(this)
        txtAddMore.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        createDataForDropDown()
        setAdapter()
    }

    private fun createDataForDropDown() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                levelData.clear()
                for (i in 0..9) {
                    levelData.add("Level ${i + 1}")
                }

                designationData.clear()
                (appDatabase?.designationDataListDao()?.allDesignations)?.forEach() {
                    designationData.add(it!!)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    addContactItem()
                    addContactAdapter?.updateListData(levelData, designationData)
                    addContactAdapter?.notifyItemChanged(arrayListOfData.size)
                }
            }
        }

    }

    private fun setAdapter() {
        addContactAdapter = AddContactAdapter(this@AddMoreContactActivity, arrayListOfData)
        rcvContact.adapter = addContactAdapter
        addContactAdapter?.setClickListener(this)
    }

    private fun addContactItem() {
        if (arrayListOfData.size == 0) {
            arrayListOfData.add(0, createEmptyAddContactModel())
        }
        addContactAdapter?.updateListData(levelData, designationData)
        addContactAdapter?.notifyItemChanged(arrayListOfData.size)
    }

    private fun createEmptyAddContactModel(): TeaEstateAddContactModel {
        return TeaEstateAddContactModel(
            contact_id = System.currentTimeMillis(),
            contact_email = "",
            contact_name = "",
            designation = "",
            level = "",
            mobile_number = "",
            wa_number = ""
        )
    }

    override fun onClickView(view: View, subMenu: TeaEstateAddContactModel, position: Int) {

    }

}