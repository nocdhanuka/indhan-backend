package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.adapter

import android.annotation.SuppressLint
import android.app.Activity
import android.graphics.Color
import android.os.Build
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.AddIfcFarmerRegistrationActivity
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import kotlinx.android.synthetic.main.add_ifc_farmer_row.view.*

class AddIfcFarmerAdapter(
    private val filteredData: MutableList<AddIfcFarmer>?,
    private val mActivityContext: Activity,
    ifcFarmerRegistration: AddIfcFarmerRegistrationActivity
) : RecyclerView.Adapter<AddIfcFarmerAdapter.MyViewHolder>() {

    private var clickListener: AddIfcFarmerClickListener = ifcFarmerRegistration

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

        init {

            itemView.etFarmerName.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {
                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}

                override fun afterTextChanged(s: Editable?) {
                    if (!TextUtils.isEmpty(s.toString().trim())) {
                        filteredData!![absoluteAdapterPosition].farmer_name = s.toString().trim()
                    }
                }
            })

            itemView.etRetMobileNumber.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence,
                    start: Int,
                    count: Int,
                    after: Int
                ) {
                }

                override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {

                }

                override fun afterTextChanged(s: Editable?) {
                    if (s != null) {
                        val stringData = s.toString().replace(" ", "")
                        if (s.contains(" ")) {
                            itemView.etRetMobileNumber.setText(stringData)
                            itemView.etRetMobileNumber.setSelection(stringData.length)
                            return
                        }
                        if (stringData.isNotEmpty()) {
                            if (stringData[0] == '0' || stringData[0] == '1' || stringData[0] == '2'
                                || stringData[0] == '3' || stringData[0] == '4' || stringData[0] == '5'
                            ) {
                                itemView.etRetMobileNumber.setText(
                                    stringData[0].toString().replace(stringData[0].toString(), "")
                                )
                            } else if (stringData.length != 10 && stringData.isNotEmpty()) {
                                itemView.tilMobileNumber.error = "Enter a 10 digit number"
                                itemView.etRetMobileNumber.setTextColor(Color.parseColor("#C11F1F"))
                            } else {
                                itemView.tilMobileNumber.isErrorEnabled = false
                                itemView.etRetMobileNumber.setTextColor(Color.parseColor("#0EA222"))
                            }
                        }
                        filteredData!![absoluteAdapterPosition].mobile = stringData
                    }
                }
            })

            itemView.etAcers.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                }

                override fun afterTextChanged(s: Editable?) {
                    if (!TextUtils.isEmpty(s.toString().trim())) {
                        var acers = "0"
                        try {
                            acers = s.toString().trim().toDouble().toString()
                        } catch (e: Exception) {
                            e.printStackTrace()
                        }
                        if (absoluteAdapterPosition != -1)
                            filteredData!![absoluteAdapterPosition].acrers = acers

                    }
                }

            })

            itemView.ivFarmerImage.setOnClickListener {
                if (absoluteAdapterPosition != -1)
                    clickListener.onClickImage(
                        it,
                        filteredData!![absoluteAdapterPosition],
                        absoluteAdapterPosition
                    )
            }
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): AddIfcFarmerAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.add_ifc_farmer_row, parent, false)
        return MyViewHolder(itemView)
    }

    @SuppressLint("NotifyDataSetChanged")
    override fun onBindViewHolder(holder: AddIfcFarmerAdapter.MyViewHolder, position: Int) {
        if (filteredData?.size == 1) {
            holder.itemView.ivDelete.visibility = View.GONE
        } else {
            if (filteredData != null)
                if ((filteredData.size - 1) == holder.absoluteAdapterPosition) {
                    holder.itemView.ivDelete.visibility = View.VISIBLE
                } else {
                    holder.itemView.ivDelete.visibility = View.GONE
                }
        }

        holder.itemView.ivDelete.setOnClickListener {
            clickListener.onDeleteRow(holder.absoluteAdapterPosition)
        }

        holder.itemView.etFarmerName.setText(filteredData!![position].farmer_name)
        holder.itemView.etRetMobileNumber.setText(filteredData[position].mobile)
        holder.itemView.etAcers.setText(filteredData[position].acrers)

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            holder.itemView.etFarmerName.focusable
        } else {
            holder.itemView.etFarmerName.requestFocus()
        }

        if (filteredData.size - 1 == position) {
            holder.itemView.etRetMobileNumber.requestFocus()
        }

    }

    override fun getItemCount(): Int {
        return filteredData?.size ?: 0
    }

    interface AddIfcFarmerClickListener {
        fun onClickImage(view: View, addIfcFarmerModel: AddIfcFarmer, position: Int)
        fun onDeleteRow(position: Int)
    }
}
