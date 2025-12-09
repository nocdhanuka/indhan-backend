package com.dhanuka.indhan.institutional.adapter

import android.content.Context
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.AddTestimonialsModel
import com.dhanuka.indhan.institutional.model.TestimonialListData
import com.dhanuka.indhan.institutional.model.influencers.InfluencerTestimonialData
import kotlinx.android.synthetic.main.item_add_testimonials.view.*
import java.util.*

class AddTestimonialDataAdapter(
    private val context: Context,
    private val arrayListOfData: LinkedList<AddTestimonialsModel>
) : RecyclerView.Adapter<AddTestimonialDataAdapter.ViewHolder>() {
    private var type = ""
    private lateinit var clickListener: OnClickItem
    private var testimonialStringData: ArrayList<String> = ArrayList()
    private var selectLevel: NumberSpinner? = null

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            itemView.txtAddProperitorList.addTextChangedListener(object : TextWatcher {
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
                    arrayListOfData[absoluteAdapterPosition].testimonialMessage =
                        (s ?: "").toString()
                }
            })
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): AddTestimonialDataAdapter.ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_add_testimonials, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: AddTestimonialDataAdapter.ViewHolder, position: Int) {

        if (!TextUtils.isEmpty(arrayListOfData[holder.absoluteAdapterPosition].testimonialName)) {
            holder.itemView.tvSelectTestimonials.text =
                arrayListOfData[holder.absoluteAdapterPosition].testimonialName
        } else {
            holder.itemView.tvSelectTestimonials.text = ""
        }
        if (!TextUtils.isEmpty(arrayListOfData[holder.absoluteAdapterPosition].testimonialMessage)) {
            holder.itemView.txtAddProperitorList.setText(arrayListOfData[holder.absoluteAdapterPosition].testimonialMessage)
        } else {
            holder.itemView.txtAddProperitorList.setText("")
        }

//        selectLevel =
//            NumberSpinner(context as Activity, testimonialStringData, "Select Testimonial")
//        selectLevel?.bindOnSpinerListener(object : OnSpinerItemClick {
//            override fun onClick(item: String, position: Int) {
//                holder.itemView.tvSelectTestimonials.text = item
//                arrayListOfData[holder.absoluteAdapterPosition].testimonialId =
//                    testimonialData[position].institutional_testimonial_id.toString()
//                notifyItemRangeChanged(0, (arrayListOfData.size - 1))
//            }
//        })

        holder.itemView.tvSelectTestimonials.setOnClickListener {
            if (testimonialStringData.size > 0) {
                clickListener.onClickView(
                    it,
                    arrayListOfData[holder.absoluteAdapterPosition],
                    holder.absoluteAdapterPosition,
                    type
                )
            }
        }
    }

    override fun getItemCount(): Int = arrayListOfData.size
    fun updateListData(
        levelList: ArrayList<TestimonialListData>,
        type: String,
        listOfDataTestimonialInfluencer: ArrayList<InfluencerTestimonialData>
    ) {
        this.type = type
        if (type == "3") {
            testimonialStringData.clear()
            listOfDataTestimonialInfluencer.forEach() {
                testimonialStringData.add(it.testimonial)
            }
        } else {
            testimonialStringData.clear()
            levelList.forEach() {
                testimonialStringData.add(it.testimonial_name.toString())
            }
        }

    }

    interface OnClickItem {
        fun onClickView(view: View, subMenu: AddTestimonialsModel, position: Int, type: String)
    }

}