package com.dhanuka.indhan.Activity

import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.util.TypedValue
import android.view.View
import android.view.WindowManager
import com.dhanuka.indhan.Activity.LoginMVP.LoginContracts
import com.dhanuka.indhan.Activity.LoginMVP.LoginPresenterImpl
import com.dhanuka.indhan.Activity.LoginMVP.Model.LoginBean
import com.dhanuka.indhan.MenuScreenActivity.View.DynamicMenuActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.analytics.FAEventConstants
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Util
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_login.*
import java.util.*

/**
 * Created by Shivam on 2/20/2018.
 */

class LoginActivity : BaseActivity(), LoginContracts.ILoginView {
    override fun failure(items: String) {
        makeToast(items)
    }

    var testUser = "591919"
    var pass = "DZLPH7P9"
    //Dhanuka doctor


    private lateinit var preference: Preference
    private lateinit var mloginPresenter: LoginContracts.LoginPresenter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)
        preference = Preference.getInstance(this)
        initListners()
        mloginPresenter = LoginPresenterImpl(this)
//        etRetEmpId.setText(testUser)
//        etRetPassword.setText(pass)
    }

    private fun initListners() {
        etRetEmpId.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {}

            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {}

            override fun afterTextChanged(s: Editable) {
                if (s.isEmpty()) {
                    etRetEmpId.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14f)
                } else {
                    etRetEmpId.setTextSize(TypedValue.COMPLEX_UNIT_SP, 20f)
                }
                val result = s.toString().replace(" ".toRegex(), "")
                if (s.toString() != result) {
                    etRetEmpId.setText(result)
                    etRetEmpId.setSelection(result.length)
                }
                validateEmpId()
            }
        })

        etRetPassword.addTextChangedListener(object : TextWatcher {

            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {}

            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {}

            override fun afterTextChanged(s: Editable) {
                if (s.isEmpty()) {
                    etRetPassword.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14f)
                } else {
                    etRetPassword.setTextSize(TypedValue.COMPLEX_UNIT_SP, 20f)
                }
                val result = s.toString().replace(" ".toRegex(), "")
                if (s.toString() != result) {
                    etRetPassword.setText(result)
                    etRetPassword.setSelection(result.length)
                }
                validatePassword()
            }
        })
        btnRbLogin.setOnClickListener(this)
        val filterArray1 = arrayOf(Util.getEditTextFilterEmoji())
        etRetEmpId.filters = filterArray1
        val filterArray2 = arrayOf(Util.getEditTextFilterEmoji())
        etRetPassword.filters = filterArray2
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.btnRbLogin -> {
                hideSoftKeyboard()
                if (checkAllInput()) {
                    val query = HashMap<String, Any>()
                    query["username"] = etRetEmpId.text.toString().trim()
                    query["password"] = etRetPassword.text.toString().trim()
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        mloginPresenter.onSubmitClick(query)
                    } else {
                        makeToast(resources.getString(R.string.no_internet_connection))
                    }
                }
            }
        }
    }

    private fun checkAllInput(): Boolean {
        if (!validateEmpId()) {
            return false
        }
        return if (!validatePassword()) {
            return false
        } else true
    }

    override fun hideProgress() {
        dismissProgress()
    }

    override fun setItems(items: LoginBean) {
        when (items.statusCode) {
            200 -> {
                makeToast(items.message)
                preference.putBolean("isLogin", true)
                preference.putString("user_id", items.data?.user_id.toString())
                preference.putString("token", items.data?.apiToken)
                preference.putString("ecn", items.data?.ecn)
                when {
                    (items.data?.userType.equals("dhanuka_doctor")) -> {
                        preference.putString(
                            "activity_permission",
                            "${items.data?.activityPermission}"
                        )
                    }
                    else -> {
                        preference.putString("activity_permission", "2")
                    }
                }
                preference.putString("user_type", items.data?.userType)
                preference.putString("Username", items.data?.username)
                if (items.data?.userState!!.size > 0) {
                    preference.putString("state", items.data?.userState!![0].item)
                    preference.putInt("state_id", items.data?.userState!![0].sid)
                }
                preference.putString("designation", items.data?.designation)
                launchIntent(DynamicMenuActivity::class.java, true)
            }
            409 -> {
                val preference: Preference = Preference.getInstance(this)
                preference.putString("ticketId", "")
                preference.putString("ticketTime", "")
                preference.putBolean("isLogin", false)
                preference.putString("token", "")
                makeToast(items.message)
                launchIntent(LoginActivity::class.java, true)
            }
            else -> {
                makeToast(items.message)
            }
        }
        val query = HashMap<String, Any>()
        query["username"] = etRetEmpId.text.toString().trim()
        query["password"] = etRetPassword.text.toString().trim()
        query["message"] = items.message ?: "Custom Message -> Message is null"
        query["api_status"] = items.statusCode.toString()
        Utility.logEvent(LoginActivity::class.java.toString(), FAEventConstants.loginEvent, query)
    }

    private fun validatePassword(): Boolean {
        val password = etRetPassword.text.toString().trim { it <= ' ' }
        if (password.isEmpty() || !isValidPassword(password)) {
            tilPassword.error = getString(R.string.err_msg_password)
            requestFocus(etRetPassword)
            return false
        } else {
            tilPassword.isErrorEnabled = false
        }
        return true
    }

    private fun validateEmpId(): Boolean {
        val emp = etRetEmpId.text.toString().trim { it <= ' ' }
        if (emp.isEmpty() || !isValidEmpId(emp)) {
            tilEmpId.error = getString(R.string.err_msg_emp)
            requestFocus(etRetEmpId)
            return false
        } else {
            tilEmpId.isErrorEnabled = false
        }
        return true
    }

    private fun requestFocus(view: View) {
        if (view.requestFocus()) {
            window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_VISIBLE)
        }
    }

    fun getRandomString(sizeOfRandomString: Int): String {
        val allowed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
        val random = Random()
        val sb = StringBuilder(sizeOfRandomString)
        for (i in 0 until sizeOfRandomString)
            sb.append(allowed[random.nextInt(allowed.length)])
        return sb.toString()
    }

}
