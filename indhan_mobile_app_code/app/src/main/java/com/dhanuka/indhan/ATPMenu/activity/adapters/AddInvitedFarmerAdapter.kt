package com.dhanuka.indhan.ATPMenu.activity.adapters

import android.content.Context
import android.graphics.Color
import androidx.recyclerview.widget.RecyclerView
import android.text.Editable
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import com.dhanuka.indhan.ATPMenu.activity.models.InviteFarmerList
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_add_invited_farmer.view.*

class AddInvitedFarmerAdapter(
    context: Context,
    atpBean: MutableList<InviteFarmerList>,
    var imgAdd: ImageView
) : RecyclerView.Adapter<AddInvitedFarmerAdapter.ViewHolder>() {
    var context: Context
    var atpBean: MutableList<InviteFarmerList>

    init {
        this.context = context
        this.atpBean = atpBean
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_add_invited_farmer, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return atpBean.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        if (position == 0) {
            holder.view.viewLine.visibility = View.GONE
            holder.view.edtFullName.isEnabled = true
            holder.view.edtContactsNo.isEnabled = true
            holder.view.edtFullName.setText("")
            holder.view.edtContactsNo.setText("")
            holder.view.txtAddNew.visibility = View.GONE
            holder.view.llName.setBackgroundResource(R.drawable.input_border)
            holder.view.llContacts.setBackgroundResource(R.drawable.input_border)
        } else {
            holder.view.edtFullName.isEnabled = false
            holder.view.edtContactsNo.isEnabled = false
            holder.view.edtFullName.setText(atpBean[position].farmerName)
            holder.view.edtContactsNo.setText(atpBean[position].contactNumber)
            holder.view.txtAddNew.visibility = View.GONE
            holder.view.llName.setBackgroundResource(R.drawable.input_border_white)
            holder.view.llContacts.setBackgroundResource(R.drawable.input_border_white)
            holder.view.viewLine.visibility = View.VISIBLE
            holder.view.edtContactsNo.setTextColor(Color.parseColor("#000000"))
            holder.view.edtFullName.setTextColor(Color.parseColor("#000000"))
        }

        holder.view.edtContactsNo.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {
            }

            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {

            }

            override fun afterTextChanged(s: Editable?) {
                System.out.println(s.toString())
                if (s != null) {
                    val stringData = s.toString().replace(" ", "")
                    if (s.contains(" ")) {
                        holder.view.edtContactsNo.setText(stringData)
                        holder.view.edtContactsNo.setSelection(stringData.length)
                        return
                    }
                    if (stringData.isNotEmpty()) {
                        if (stringData[0].equals('0') || stringData[0].equals('1') || stringData[0].equals(
                                '2'
                            )
                            || stringData[0].equals('3') || stringData[0].equals('4') || stringData[0].equals(
                                '5'
                            )
                        ) {
                            holder.view.edtContactsNo.setText(
                                stringData[0].toString().replace(stringData[0].toString(), "")
                            )
                        } else if (stringData.length != 10 && stringData.isNotEmpty()) {
                            // holder.view.edtContactsNo.error = "Enter a 10 digit number"
                            holder.view.edtContactsNo.setTextColor(Color.parseColor("#C11F1F"))
                        } else {
                            //  holder.view.edtContactsNo.error = null
                            holder.view.edtContactsNo.setTextColor(Color.parseColor("#0EA222"))
                        }
                    }
                }
            }
        })
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }

}