package com.dhanuka.indhan.support.view

//import com.dhanuka.indhan.support.model.option
import android.content.Context
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.Toast
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.View.CustomProgressDialog
import com.dhanuka.indhan.support.RetroInstance
import com.dhanuka.indhan.support.RetrofitService
import com.dhanuka.indhan.support.adapter.OptionsAdapter
import com.dhanuka.indhan.support.adapter.QuestionAdapter
import com.dhanuka.indhan.support.adapter.SubmissionAdapter
import com.dhanuka.indhan.support.adapter.TextAnswerAdapter
import com.dhanuka.indhan.support.model.*
import kotlinx.android.synthetic.main.activity_hr_related.*
import kotlinx.android.synthetic.main.activity_support_menu.recyclerList
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class QaActivity() : BaseActivity(), OptionsAdapter.OptionClickListener,
    TextAnswerAdapter.TextClickListener, SubmissionAdapter.SubmitClickListener {
    private var progressDialog: CustomProgressDialog? = null
    private lateinit var toast: Toast
    lateinit var adapter: QuestionAdapter
    lateinit var options: OptionsAdapter
    lateinit var textAnswer: TextAnswerAdapter
    lateinit var submissionAnswer: SubmissionAdapter
    var list: ArrayList<QuestionModel> = ArrayList<QuestionModel>()
    var option: ArrayList<Option> = ArrayList<Option>()
    var submission: ArrayList<SubmitResponseModel> = ArrayList<SubmitResponseModel>()
    var listQA: List<QuestionTypeModel> = listOf()
    var listTextAnswer: ArrayList<TextAnswer> = ArrayList<TextAnswer>()
    var postResponse = PostDataResponse()
    private lateinit var preference: Preference

    var questionIds: ArrayList<String> = ArrayList<String>()
    var answers: ArrayList<String> = ArrayList<String>()
    private lateinit var userEcn: String
    private lateinit var id: String
    private lateinit var campName: String
    override fun onCreate(savedInstanceState: Bundle?) {
        id = intent.getIntExtra("id", 1).toString()
        campName = intent?.getStringExtra("campName") ?: ""
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_hr_related)
        preference = Preference(this)
        toast = Toast(this)
        userEcn = preference.getString("ecn")
        iCrossQA.setOnClickListener { view ->
            finish()
        }
        if (campName.length > 30) {
            val cName: String = campName.subSequence(0, 29).toString() + "..."
            title_campaign.text = cName
        } else {
            title_campaign.text = campName
        }
        recyclerList.layoutManager =
            GridLayoutManager(this, 1)
        optionRec.layoutManager =
            LinearLayoutManager(this, RecyclerView.VERTICAL, false)
        options = OptionsAdapter(option)
        submissionAnswer = SubmissionAdapter(submission)
        textAnswer = TextAnswerAdapter(listTextAnswer)
        adapter = QuestionAdapter(list)
        recyclerList.adapter = adapter;
        adapter.notifyDataSetChanged()
        options.setClickListener(this)
        submissionAnswer.setClickListener(this)
        textAnswer.setClickListener(this)
        showProgress()
        getQuestions(this, this.id.toInt(), this)

    }

    override fun onClick(p0: View?) {
    }


    fun getQA(context: Context, id: Int, click: OptionsAdapter.OptionClickListener) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.getQA(id)
        call.enqueue(object : Callback<List<QuestionTypeModel>> {
            override fun onResponse(
                call: Call<List<QuestionTypeModel>>,
                response: Response<List<QuestionTypeModel>>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    listQA = responseBody
                    adapter = QuestionAdapter(list)
                    list.add(
                        QuestionModel(
                            listQA[0].campaign_que_id.toString(),
                            listQA[0].question_text.toString(),
                            listQA[0].option_type.toString(),
                            3,
                            getResources().getDrawable(R.drawable.rounded_corner_red)
                        )
                    )
                    questionIds.add(listQA[0].campaign_que_id.toString())
                    recyclerList.adapter = adapter
                    option.clear()
                    if (listQA[0].option_type?.toInt() == 1) {
                        for (item in listQA[0].options!!) {
                            option.add(item)
                        }
                    }
                    options.notifyDataSetChanged()
                    adapter.notifyDataSetChanged()
                    optionRec.adapter = options
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    )
                        .show()
                }
                hideProgress()
            }

            override fun onFailure(call: Call<List<QuestionTypeModel>>, t: Throwable) {
                hideProgress()
                Toast.makeText(context, "Something went wrong!", Toast.LENGTH_LONG).show()
            }
        })
    }

    fun sumbit(context: Context, id: Int) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.postResponse(
            PostData(
                id,
                userEcn,
                questionIds.toList(),
                answers.toList(),
                1,
                System.currentTimeMillis()
            )
        )
        call.enqueue(object : Callback<PostDataResponse> {
            override fun onResponse(
                call: Call<PostDataResponse>,
                response: Response<PostDataResponse>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    postResponse = responseBody
                    if (responseBody.status == 200) {
                        Toast.makeText(
                            context,
                            "Campaign Submission Successful!",
                            Toast.LENGTH_LONG
                        )
                            .show()
                        launchIntent(SupportActivity::class.java, false)
                    } else {
                        Toast.makeText(context, "Campaign Submission Failed!", Toast.LENGTH_LONG)
                            .show()
                    }
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    )
                        .show()
                }
            }

            override fun onFailure(call: Call<PostDataResponse>, t: Throwable) {
                hideProgress()
                Toast.makeText(context, "Something went wrong!", Toast.LENGTH_LONG).show()
            }
        })
    }

    fun getQuestions(context: Context, id: Int, click: OptionsAdapter.OptionClickListener) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.getQA(id)
        call.enqueue(object : Callback<List<QuestionTypeModel>> {
            override fun onResponse(
                call: Call<List<QuestionTypeModel>>,
                response: Response<List<QuestionTypeModel>>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    if (responseBody.isNotEmpty() || responseBody != null) {
                        listQA = responseBody
                        checkFilled(context, id, click)
                    } else {
                        hideProgress()
                        Toast.makeText(
                            context,
                            "This campaign has no questions!",
                            Toast.LENGTH_LONG
                        )
                            .show()
                    }
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    )
                        .show()
                }
            }

            override fun onFailure(call: Call<List<QuestionTypeModel>>, t: Throwable) {
                hideProgress()
                Toast.makeText(context, "Something went wrong! $t", Toast.LENGTH_LONG).show()
            }
        })
    }

    fun checkFilled(context: Context, id: Int, click: OptionsAdapter.OptionClickListener) {
        val retroInstance = RetroInstance.getRetrofitInstance().create(RetrofitService::class.java)
        val call = retroInstance.checkFilled(userEcn, id)
        call.enqueue(object : Callback<FilledCampaign> {
            override fun onResponse(
                call: Call<FilledCampaign>,
                response: Response<FilledCampaign>
            ) {
                if (response.body() != null) {
                    val responseBody = response.body()!!
                    if (responseBody.data.isNotEmpty()) {
                        if (responseBody.data.last().campaignId == id) {
                            val qList = responseBody.data.last().questionList
                                .replace("[", "")
                                .replace("]", "")
                                .split(",")
                            val aList = responseBody.data.last().answerList
                                .replace("[", "")
                                .replace("]", "")
                                .split(",")
                            val qIds: ArrayList<Int> = ArrayList<Int>()
                            val answers: ArrayList<String> = ArrayList<String>()
                            for (qString in qList) {
                                qIds.add(
                                    qString.replace("/[^0-9]+/", "")
                                        .replace("\"", "")
                                        .toInt()
                                )
                            }
                            for (aString in aList) {
                                answers.add(aString.replace("\"", ""))
                            }
                            var index = 0
                            for (q in qIds) {
                                if (qIds.last() == q) {
                                    for (question in listQA) {
                                        if (question.campaign_que_id == q) {
                                            list.add(
                                                QuestionModel(
                                                    "1",
                                                    question.question_text.toString(),
                                                    "type",
                                                    3,
                                                    getResources().getDrawable(R.drawable.rounded_corner_red)
                                                )
                                            )
                                        }
                                    }
                                } else {
                                    for (question in listQA) {
                                        if (question.campaign_que_id == q) {
                                            list.add(
                                                QuestionModel(
                                                    "1",
                                                    question.question_text.toString(),
                                                    "type",
                                                    3,
                                                    getResources().getDrawable(R.drawable.rounded_corner_red)
                                                )
                                            )
                                            list.add(
                                                QuestionModel(
                                                    "1",
                                                    answers[index].toString(),
                                                    "type",
                                                    5,
                                                    getResources().getDrawable(R.drawable.rounded_corner)
                                                )
                                            )
                                        }
                                    }
                                }
                                index++
                            }
                            adapter.notifyDataSetChanged()
                            hideProgress()

                        } else {
                            getQA(context, id, click)
                        }
                    } else {
                        getQA(context, id, click)
                    }
                } else {
                    Toast.makeText(
                        context,
                        "Server error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    )
                        .show()
                }

            }

            override fun onFailure(call: Call<FilledCampaign>, t: Throwable) {
                print(t)
            }
        })
    }

    override fun onClick(view: View, option: Option) {
        answers.add(option.optionValue!!)
        list.add(
            QuestionModel(
                listQA[list.size - 1].campaign_que_id.toString(),
                option.optionValue,
                listQA[list.size - 1].option_type.toString(),
                5,
                getResources().getDrawable(R.drawable.rounded_corner)
            )
        )
        var proceed: String = "0"
        for (q in listQA) {
            if (q.question_number == option.proceedTo) {
                questionIds.add(q.campaign_que_id.toString())
                list.add(
                    QuestionModel(
                        q.campaign_que_id.toString(),
                        q.question_text.toString(),
                        q.option_type.toString(),
                        3,
                        getResources().getDrawable(R.drawable.rounded_corner_red)
                    )
                )
                proceed = q.proceedTo.toString()
            }
        }
        adapter.notifyDataSetChanged()

        if (list.last().optionType.toInt() == 1) {
            this.option.clear()
            for (q in listQA) {
                if (q.campaign_que_id == list.last().id.toInt()) {
                    for (item in q.options!!) {
                        this.option.add(item)
                    }
                }
            }
            options.notifyDataSetChanged()
            adapter.notifyDataSetChanged()
            optionRec.adapter = options
        }


        if (list.last().optionType.toInt() == 2) {
            val v: View = LayoutInflater.from(this).inflate(R.layout.layout_text_answer, null)
            listTextAnswer.add(TextAnswer("Enter your answer", proceed.toInt()))
            optionRec.adapter = textAnswer
            textAnswer.setClickListener(this)
            textAnswer.notifyDataSetChanged()
        }
        if (list.last().optionType.toInt() == 3) {
            this.option.clear()
            this.submission.clear()
            this.submission.add(SubmitResponseModel("Submit", "0"))
            optionRec.adapter = submissionAnswer
            submissionAnswer.setClickListener(this)
            submissionAnswer.notifyDataSetChanged()
        }
    }


    override fun onClick(view: View, text: TextAnswer) {
        answers.add(text.answerText)
        list.add(
            QuestionModel(
                "0",
                text.answerText,
                text.proceedTo.toString(),
                5,
                getResources().getDrawable(R.drawable.rounded_corner)
            )
        )
        var proceed: String = "0"
        for (q in listQA) {
            if (q.question_number == text.proceedTo) {
                questionIds.add(q.campaign_que_id.toString())
                list.add(
                    QuestionModel(
                        q.campaign_que_id.toString(),
                        q.question_text.toString(),
                        q.option_type.toString(),
                        3,
                        getResources().getDrawable(R.drawable.rounded_corner_red)
                    )
                )
                proceed = q.proceedTo.toString()
            }
        }
        adapter.notifyDataSetChanged()

        if (list.last().optionType.toInt() == 1) {
            this.option.clear()
            for (q in listQA) {
                if (q.campaign_que_id == list.last().id.toInt()) {
                    for (item in q.options!!) {
                        this.option.add(item)
                    }
                }
            }
            options.notifyDataSetChanged()
            adapter.notifyDataSetChanged()
            optionRec.adapter = options
        }


        if (list.last().optionType.toInt() == 2) {
            listTextAnswer.clear()
            val v: View = LayoutInflater.from(this).inflate(R.layout.layout_text_answer, null)
            listTextAnswer.add(TextAnswer("Enter your text", proceed.toInt()))
            optionRec.adapter = textAnswer
            textAnswer.setClickListener(this)
            textAnswer.notifyDataSetChanged()
        }


        if (list.last().optionType.toInt() == 3) {
            this.option.clear()
            this.submission.clear()
            this.submission.add(SubmitResponseModel("Submit", "0"))
            optionRec.adapter = submissionAnswer
            submissionAnswer.setClickListener(this)
            submissionAnswer.notifyDataSetChanged()
        }
    }

    override fun onClick(view: View, submit: SubmitResponseModel) {
        sumbit(this, this.id.toInt())
    }


}