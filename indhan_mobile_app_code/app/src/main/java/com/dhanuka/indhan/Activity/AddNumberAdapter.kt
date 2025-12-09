package com.dhanuka.indhan.Activity

import android.app.Activity
import android.graphics.Color
import android.os.Build
import android.text.Editable
import android.text.TextWatcher
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.WindowManager
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Model.AddNumberModel
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.add_number_row.view.*

/**
 * Created by Shivam on 3/8/2018.
 */

class AddNumberAdapter(
    private val filteredData:
    MutableList<AddNumberModel>?,
    private val mActivityContext: Activity
) :
    RecyclerView.Adapter<AddNumberAdapter.MyViewHolder>() {

    private lateinit var clickListener: AddNumberClickListener

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

        init {
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
                    System.out.println(s.toString())
                    if (s != null) {
                        var stringData = s.toString().replace(" ", "")
                        if (s.contains(" ")) {
                            itemView.etRetMobileNumber.setText(stringData)
                            itemView.etRetMobileNumber.setSelection(stringData.length)
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
                                itemView.etRetMobileNumber.setText(
                                    stringData[0].toString().replace(stringData[0].toString(), "")
                                )
//                                itemView.tilMobileNumber.error = "Enter a valid mobile number."
                                requestFocus(itemView.etRetMobileNumber)
//                                itemView.etRetMobileNumber.setTextColor(Color.parseColor("#C11F1F"))
                            } else if (stringData.length != 10 && stringData.length > 0) {
                                itemView.tilMobileNumber.error = "Enter a 10 digit number"
                                requestFocus(itemView.etRetMobileNumber)
                                itemView.etRetMobileNumber.setTextColor(Color.parseColor("#C11F1F"))
                            } else {
                                itemView.tilMobileNumber.isErrorEnabled = false
                                itemView.etRetMobileNumber.setTextColor(Color.parseColor("#0EA222"))
                            }
                        }
                        if (absoluteAdapterPosition != -1) {
                            filteredData!![absoluteAdapterPosition].number = stringData
                        }
                    }
                }
            })

            view.setOnClickListener { view ->
                clickListener.onClick(
                    view,
                    filteredData?.get(absoluteAdapterPosition)!!,
                    absoluteAdapterPosition
                )
            }
        }
    }

    private fun requestFocus(view: View) {
        if (view.requestFocus()) {
            mActivityContext.window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_VISIBLE)
        }
    }

    fun setClickListener(itemClickListener: AddNumberClickListener) {
        this.clickListener = itemClickListener
    }


    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): AddNumberAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.add_number_row, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: AddNumberAdapter.MyViewHolder, position: Int) {
        val addNumberModel = filteredData!![position]
        holder.itemView.etRetMobileNumber.setText(filteredData[position].number)
        Log.e("values filter upar", "" + filteredData[position].number)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            holder.itemView.etRetMobileNumber.focusable
        } else {
            holder.itemView.etRetMobileNumber.requestFocus()
        }

        holder.itemView.ivRemove.setOnClickListener {
            if (position < filteredData.size) {
                if (filteredData.size == 1) {
                    filteredData[position].number = ""
                    holder.itemView.etRetMobileNumber.setText("")
                    notifyDataSetChanged()
                } else {
                    filteredData.removeAt(position)
                    notifyDataSetChanged()
                }
            }

        }


        if (filteredData.size - 1 == position) {
            holder.itemView.etRetMobileNumber.requestFocus()
        }

    }

    override fun getItemCount(): Int {
        return filteredData?.size ?: 0
    }

}
