<?php

/* @galleries/shortcode/style.twig */
class __TwigTemplate_001dc47f28c9bbe1acde217cce3cb3ba7dda56ed9c04b46843aec1a2852dd329 extends Twig_SupTwg_Template
{
    public function __construct(Twig_SupTwg_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
    }

    // line 1
    public function getprop($__prop__ = null, $__value__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "prop" => $__prop__,
            "value" => $__value__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            echo Twig_SupTwg_escape_filter($this->env, ($context["prop"] ?? null), "html", null, true);
            echo ":";
            echo Twig_SupTwg_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo ";";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_SupTwg_Markup($tmp, $this->env->getCharset());
    }

    // line 2
    public function getobject($__values__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "values" => $__values__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            $context['_parent'] = $context;
            $context['_seq'] = Twig_SupTwg_ensure_traversable(($context["values"] ?? null));
            foreach ($context['_seq'] as $context["prop"] => $context["value"]) {
                echo Twig_SupTwg_escape_filter($this->env, $context["prop"], "html", null, true);
                echo ":";
                echo Twig_SupTwg_escape_filter($this->env, $context["value"], "html", null, true);
                echo ";";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['prop'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_SupTwg_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "@galleries/shortcode/style.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 2,  21 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_SupTwg_Source("", "@galleries/shortcode/style.twig", "/home/rgoalin/domains/rgoal.in/public_html/wp-content/plugins/gallery-by-supsystic/src/GridGallery/Galleries/views/shortcode/style.twig");
    }
}
